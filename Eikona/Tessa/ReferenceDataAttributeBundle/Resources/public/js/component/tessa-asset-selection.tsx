import * as React from "react";
import __ from 'akeneoreferenceentity/tools/translator';
import TessaAssetSelectionModal from './tessa-asset-selection-modal'
import {TessaAttribute} from "../reference-entity/attribute/tessa";
const routing = require('routing');

interface Props {
  selectedAssetIds: string[];
  onChange: (newAssetIds: string[]) => void;
  canEditData: boolean;
  identifier: string;
  attribute: TessaAttribute,
  locale: string|null;
  channel: string|null;
}

export default class TessaAssetSelection extends React.Component<Props, { modalVisible: boolean }> {

  constructor(props: Props) {
    super(props);
    this.state = {
      modalVisible: false
    };
  }

  showModal(event: React.MouseEvent) {
    if (!this.props.canEditData) {
      event.preventDefault();
      return;
    }

    this.setState({modalVisible: true});
  }

  onModalConfirm (assetIds: string[]) {
    this.props.onChange(assetIds);
    this.setState({modalVisible: false});
  }

  onModalCancel() {
    this.setState({modalVisible: false});
  }

  removeAsset(event: React.MouseEvent, id: string) {
    if (!this.props.canEditData) {
      event.preventDefault();
      return;
    }

    this.props.onChange(this.props.selectedAssetIds.filter((assetId) => assetId !== id));
  }

  items(): Array<{ id: string, linkUrl: string, url: string }> {
    return this.props.selectedAssetIds.map((assetId) => ({
      id: assetId,
      url: routing.generate('eikona_tessa_media_preview', {assetId: assetId}),
      linkUrl: routing.generate('eikona_tessa_media_detail', {assetId: assetId}),
    }));
  }

  render() {
    const canEditAssetsInAkeneoUi = this.props.attribute.canEditAssetsInAkeneoUi;
    return (
      <>
        <div className={"EikonAssetGallery " + (!this.props.canEditData ? 'readonly' : '')}>
          <header className="AknAssetCollectionField-header AknButtonList AknButtonList--right">
              <span
                className={"AknButtonList-item AknButton AknButton--apply AknButton--withIcon " + (!this.props.canEditData ? 'AknButton--disabled' : '') + "add-asset icons-holder-text"}
                onClick={this.showModal.bind(this)}>
                {__('tessa.asset management.choose')}
              </span>
          </header>
          <ul className={"AknAssetCollectionField-list " + (!this.items().length ? 'empty' : '')}>
            {this.items().map((item) => (
              <li className={"asset-thumbnail-item AknAssetCollectionField-listItem"} key={item.id}>
                <a target="_blank" href={item.linkUrl}>
                  <div className="AknAssetCollectionField-assetThumbnail asset-thumbnail EikonAssetThumbnail"
                       style={{backgroundImage: `url(${item.url})`}}>
                  </div>
                </a>

                {this.props.canEditData && canEditAssetsInAkeneoUi &&
                <div className="AknButton AknButton--important AknButton--micro AknButton-squareIcon AknButton-squareIcon--delete AknAssetCollectionField-icon remove-item js-remove-asset"
                     title={__('tessa.asset management.remove')}
                     onClick={(e: React.MouseEvent) => this.removeAsset(e, item.id)}>
                </div>}
              </li>
            ))}
          </ul>
          <div className="placeholder">
            <img src="/bundles/pimui/images/Default-picture.svg" alt="upload icon"/>
          </div>
          <div className="clearfix"/>
          <footer>
            {this.props.selectedAssetIds.length}
            &nbsp;{__(this.props.selectedAssetIds.length === 1 ? 'tessa.asset management.asset' : 'tessa.asset management.assets')}
            &nbsp;{__('tessa.asset management.choosen')}
          </footer>
        </div>
        {this.state.modalVisible && (
          <TessaAssetSelectionModal
            identifier={this.props.identifier}
            locale={this.props.locale}
            channel={this.props.channel}
            attribute={this.props.attribute}
            onConfirm={this.onModalConfirm.bind(this)}
            onCancel={this.onModalCancel.bind(this)}
            selectedAssetIds={this.props.selectedAssetIds}
          />
          )}
      </>
    )
  }
}
