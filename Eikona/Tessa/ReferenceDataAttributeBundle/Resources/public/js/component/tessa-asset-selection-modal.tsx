import * as React from 'react';
import * as ReactDOM from 'react-dom';
import * as jQuery from 'jquery';
import {TessaAttribute} from "../reference-entity/attribute/tessa";
import __ from 'akeneoreferenceentity/tools/translator';
const routing = require('routing');
const LoadingMask = require('oro/loading-mask');

interface Props {
  onConfirm: (assetIds: string[]) => void;
  onCancel: () => void;
  selectedAssetIds: string[];
  identifier: string;
  attribute: TessaAttribute,
  locale: string|null;
  channel: string|null;
}

export default class TessaAssetSelectionModal extends React.Component<Props> {
  private iframe: React.RefObject<HTMLIFrameElement>;
  private modalBody: React.RefObject<HTMLDivElement>;
  private onReceiveMessageBound: any;

  constructor(props: Props) {
    super(props);

    this.iframe = React.createRef();
    this.modalBody = React.createRef();

    this.onReceiveMessageBound = this.onReceiveMessage.bind(this);
  }

  componentDidMount() {
    window.addEventListener('message', this.onReceiveMessageBound);

    // Iframe communication
    const iframe = this.iframe.current as HTMLIFrameElement;
    const iframeContent = iframe.contentWindow as Window;
    iframe.addEventListener('load', () => {
      iframeContent.postMessage(JSON.stringify({
        'selected': this.props.selectedAssetIds,
      }), '*');
    });

    // Loading Mask
    const modalBody = this.modalBody.current as HTMLDivElement;
    const loadingMask = new LoadingMask();
    loadingMask.render()
      .$el
      .appendTo(modalBody)
      .css({
        'position': 'absolute',
        'width': '100%',
        'height': '100%',
        'top': '0',
        'left': '0'
      });
    loadingMask.show();

    setTimeout(() => loadingMask.hide().$el.remove(), 5000)
  }

  componentWillUnmount(): void {
    window.removeEventListener('message', this.onReceiveMessageBound);
  }

  onReceiveMessage(event: any) {
    const receivedData = JSON.parse(event.data);
    const assetIds = receivedData.map((value: any) => value.position_asset_system_id);
    this.props.onConfirm(assetIds);
  }

  getTessaUrl(): string {
    const data = {
      ProductId: this.props.identifier,
      attribute: JSON.stringify({
        code: this.props.attribute.code.normalize(),
        type: this.props.attribute.type,
        labels: this.props.attribute.labelCollection.normalize(),
        allowed_extensions: this.props.attribute.allowedExtensions.normalize(),
        max_assets: this.props.attribute.maxAssets.normalize(),
      }),
      context: JSON.stringify({
        locale: this.props.locale,
        scope: this.props.channel,
        data: this.props.selectedAssetIds.join(','),
      })
    };

    return routing.generate('eikona_tessa_media_select', {
      data: jQuery.param(data)
    });
  }

  render() {
    const {onCancel} = this.props;

    return ReactDOM.createPortal(
      <>
        <div className="modal-backdrop in" style={{zIndex: 1040}} />
        <div className="modal EikonModalAssetsSelection in" style={{zIndex: 1041}}>
          <div className="AknFullPage">
            <div className="AknFullPage-content">
              <div>
                <div className="AknFullPage-titleContainer">
                  <div className="AknFullPage-title">{__('tessa.asset management.title')}</div>
                </div>
                <div ref={this.modalBody} className="modal-body">
                  <iframe ref={this.iframe} src={this.getTessaUrl()} />
                </div>
              </div>
            </div>
          </div>
          <div className="AknFullPage-cancel cancel" onClick={onCancel}/>
        </div>
      </>,
      document.body
    );
  }
}
