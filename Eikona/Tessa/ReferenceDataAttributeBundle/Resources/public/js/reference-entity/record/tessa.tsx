import * as React from 'react';
import Value, {NormalizedValue} from 'akeneoreferenceentity/domain/model/record/value';
import {ConcreteTessaAttribute, TessaAttribute} from '../attribute/tessa';
import ValueData from 'akeneoreferenceentity/domain/model/record/data';
import {CellView} from 'akeneoreferenceentity/application/configuration/value';
import TessaAssetSelection from '../../component/tessa-asset-selection';
import ChannelReference from 'akeneoreferenceentity/domain/model/channel-reference';
import LocaleReference from 'akeneoreferenceentity/domain/model/locale-reference';

const routing = require('routing');

class InvalidTypeError extends Error {
}

/**
 * Here we are implementing our custom Record Value model.
 */
export type NormalizedTessaData = string[];

export class TessaData extends ValueData {
  private constructor(private tessaData: string[]) {
    super();

    if (!Array.isArray(tessaData)) {
      throw new InvalidTypeError('TessaData expects a string as parameter to be created');
    }

    Object.freeze(this);
  }

  /**
   * Here, we denormalize our record value
   */
  public static createFromNormalized(tessaData: NormalizedTessaData): TessaData {
    if (null === tessaData) {
      return new TessaData([]);
    }
    return new TessaData(tessaData);
  }

  /**
   * Check the emptiness
   */
  public isEmpty(): boolean {
    return this.tessaData.length === 0;
  }

  /**
   * Check if the value is equal to the tessa data
   */
  public equals(data: ValueData): boolean {
    return data instanceof TessaData
      && this.tessaData.length === data.tessaData.length
      && !this.tessaData.some((assetId, index: number) => data.tessaData[index] !== assetId);
  }

  /**
   * The only method to implement here: the normalize method. Here you need to provide a serializable object (see https://developer.mozilla.org/en-US/docs/Glossary/Serialization)
   */
  public normalize(): string[] {
    return this.tessaData;
  }

  public toString(): string {
    return this.tessaData.join(', ');
  }
}

/**
 * The only required part of the file: exporting a denormalize method returning a tessa Record Value.
 */
export const denormalize = TessaData.createFromNormalized;

/**
 * Here we define the React Component as a function with the following props :
 *    - the custom Record Value
 *    - the callback function to update the Record Value
 *    - the right to edit the Record Value
 *
 * It returns the JSX View to display the field of the custom Record Value.
 */
const View = ({
                locale,
                channel,
                value,
                onChange,
                canEditData,
              }: {
  locale: LocaleReference,
  channel: ChannelReference,
  value: Value;
  onChange: (value: Value) => void;
  canEditData: boolean;
}) => {
  if (!(value.data instanceof TessaData && value.attribute instanceof ConcreteTessaAttribute)) {
    return null;
  }

  const attribute: TessaAttribute = value.attribute;

  const onValueChange = (newAssetIds: string[]) => {
    const newData = denormalize(newAssetIds);
    if (newData.equals(value.data)) {
      return;
    }

    const newValue = value.setData(newData);
    onChange(newValue);
  };


  // Code aus URL ermitteln, da das Attribut leider die Information nicht bekommt
  const entityCode = (location.href.match(/record\/(.+)\/enrich/) as any)[1].toUpperCase();
  const entityModule = attribute.referenceEntityIdentifier.stringValue().toUpperCase();
  const identifier = `RD.${entityModule}.${entityCode}`;

  return (
    <TessaAssetSelection
      identifier={identifier}
      locale={locale.normalize()}
      channel={channel.normalize()}
      attribute={attribute}
      canEditData={canEditData}
      onChange={onValueChange}
      selectedAssetIds={value.data.normalize()}
    />
  );
};

/**
 * The only required part of the file: exporting the custom Record Value view.
 */
export const view = View;

const memo = (React as any).memo;

/**
 * Here we define the React Component as a function with the following props :
 *    - the custom Record Value
 *
 * It returns the JSX View to display the cell of your custom Record Value in the grid.
 */
const TessaCellView: CellView = memo(({ column, value }: { column: any, value: NormalizedValue }) => {
  const tessaData = denormalize(value.data);
  const tessaAssetIds = tessaData.normalize();

  if (tessaAssetIds.length === 0) {
    return '';
  }

  let maxDisplayedAssets = column.attribute.max_displayed_assets;
  maxDisplayedAssets = Number.isInteger(maxDisplayedAssets) ? maxDisplayedAssets : 1;

  const displayedAssets = tessaAssetIds
    .slice(0, maxDisplayedAssets)
    .map((tessaAssetId) => ({
      url: routing.generate('eikona_tessa_media_preview', {assetId: tessaAssetId}),
      assetId: tessaAssetId
    }))

  const displayedAssetsRendered = displayedAssets.map((asset) => {
    return <div className="image">
      <img src={asset.url} alt={asset.assetId} />
    </div>;
  })

  const additionalAssets = tessaAssetIds
    .slice(maxDisplayedAssets)
    .map((tessaAssetId) => ({
      assetId: tessaAssetId
    }))

  let additionalAssetsRendered = null;
  if (additionalAssets.length) {
    additionalAssetsRendered = <div className="more">
      {displayedAssets.length ? '+' : ''}
      {additionalAssets.length}
    </div>
  }

  return (
    <div className="AknGrid-bodyCellContainer">
      <div className="datagrid-cell-tessa">
        {displayedAssetsRendered}
        {additionalAssetsRendered}
      </div>
    </div>
  );
});

/**
 * The only required part of the file: exporting the custom Record Value cell.
 */
export const cell = TessaCellView;

