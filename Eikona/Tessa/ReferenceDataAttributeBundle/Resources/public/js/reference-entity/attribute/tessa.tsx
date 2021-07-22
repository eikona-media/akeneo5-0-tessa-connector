import * as React from 'react';
import __ from 'akeneoreferenceentity/tools/translator';
import {getErrorsView} from 'akeneoreferenceentity/application/component/app/validation-error';
import ValidationError from "akeneoreferenceentity/domain/model/validation-error";
import Key from "akeneoreferenceentity/tools/key";
import Identifier, {createIdentifier} from 'akeneoreferenceentity/domain/model/attribute/identifier';
import ReferenceEntityIdentifier, {createIdentifier as createReferenceEntityIdentifier,} from 'akeneoreferenceentity/domain/model/reference-entity/identifier';
import LabelCollection, {createLabelCollection} from 'akeneoreferenceentity/domain/model/label-collection';
import AttributeCode, {createCode} from 'akeneoreferenceentity/domain/model/attribute/code';
import {
  Attribute,
  ConcreteAttribute,
  NormalizedAttribute,
} from 'akeneoreferenceentity/domain/model/attribute/attribute';
import {
  AllowedExtensions,
  AllowedExtensionsOptions,
  NormalizedAllowedExtensions
} from 'akeneoreferenceentity/domain/model/attribute/type/image/allowed-extensions';
import Select2 from 'akeneoreferenceentity/application/component/app/select2';

class InvalidArgumentError extends Error {
}

/**
 * This part is not mandatory but we advise you to create value object to represent your custom properties (see https://en.wikipedia.org/wiki/Value_object)
 */
type NormalizedTessaMaxAssets = number | null;

class TessaMaxAssets {
  public constructor(readonly maxAssets: number | null) {
    if (!(null === maxAssets || typeof maxAssets === 'number')) {
      throw new InvalidArgumentError('MaxAssets need to be a valid integer or null');
    }
    Object.freeze(this);
  }

  public static isValid(value: any): boolean {
    return (!isNaN(parseInt(value)) && 0 < parseInt(value)) || '' === value || null === value;
  }

  public static createFromNormalized(normalizedMaxAssets: NormalizedTessaMaxAssets) {
    return new TessaMaxAssets(normalizedMaxAssets);
  }

  public normalize() {
    return this.maxAssets;
  }

  public static createFromString(maxAssets: string) {
    if (!TessaMaxAssets.isValid(maxAssets)) {
      throw new InvalidArgumentError('MaxLength need to be a valid integer');
    }
    return new TessaMaxAssets('' === maxAssets ? null : parseInt(maxAssets));
  }

  public stringValue(): string {
    return null === this.maxAssets ? '' : this.maxAssets.toString();
  }

  public isNull(): boolean {
    return null === this.maxAssets;
  }
}

type NormalizedTessaMaxDisplayedAssets = number | null;

class TessaMaxDisplayedAssets {
  public constructor(readonly maxDisplayedAssets: number | null | undefined ) {
    if (!(null === maxDisplayedAssets || undefined === maxDisplayedAssets || typeof maxDisplayedAssets === 'number')) {
      throw new InvalidArgumentError('MaxDisplayedAssets need to be a valid integer or null');
    }
    Object.freeze(this);
  }

  public static isValid(value: any): boolean {
    return (!isNaN(parseInt(value)) && 0 <= parseInt(value)) || '' === value || null === value;
  }

  public static createFromNormalized(normalizedMaxDisplayedAssets: NormalizedTessaMaxDisplayedAssets) {
    return new TessaMaxDisplayedAssets(normalizedMaxDisplayedAssets);
  }

  public normalize() {
    return this.maxDisplayedAssets === undefined ? null : this.maxDisplayedAssets;
  }

  public static createFromString(maxDisplayedAssets: string) {
    if (!TessaMaxDisplayedAssets.isValid(maxDisplayedAssets)) {
      throw new InvalidArgumentError('MaxLength need to be a valid integer');
    }
    return new TessaMaxDisplayedAssets('' === maxDisplayedAssets ? null : parseInt(maxDisplayedAssets));
  }

  public stringValue(): string {
    return (null === this.maxDisplayedAssets || undefined === this.maxDisplayedAssets) ? '' : this.maxDisplayedAssets.toString();
  }

  public isNull(): boolean {
    return null === this.maxDisplayedAssets;
  }
}

/**
 * This type is an aggregate of all the custom properties. Here we only have one so it could seems useless but
 * here is an example with multiple properties:
 *
 *     export type TextAdditionalProperty = MaxLength | IsTextarea | IsRichTextEditor | ValidationRule | RegularExpression;
 *
 * In the example above, a additional property of a text attribute could be a Max length, is textarea, is rich text editor, ...
 */
export type TessaAdditionalProperty = TessaMaxAssets | AllowedExtensions | TessaMaxDisplayedAssets;

/**
 * Same for the non normalized form
 */
export type NormalizedTessaAdditionalProperty = NormalizedTessaMaxAssets | NormalizedAllowedExtensions | NormalizedTessaMaxDisplayedAssets;

/**
 * This interface will represent your normalized attribute (usually coming from the backend but also used in the reducer)
 */
export interface NormalizedTessaAttribute extends NormalizedAttribute {
  type: 'tessa';
  max_assets: NormalizedTessaMaxAssets;
  allowed_extensions: NormalizedAllowedExtensions;
  max_displayed_assets: NormalizedTessaMaxDisplayedAssets;
  canEditAssetsInAkeneoUi: boolean;
}

/**
 * Here we define the interface for our concrete class (our model) extending the base attribute interface
 */
export interface TessaAttribute extends Attribute {
  maxAssets: TessaMaxAssets;
  allowedExtensions: AllowedExtensions;
  maxDisplayedAssets: TessaMaxDisplayedAssets;
  canEditAssetsInAkeneoUi: boolean;

  normalize(): NormalizedTessaAttribute;
}

/**
 * Here we are starting to implement our custom attribute class.
 * Note that most of the code is due to the custom property (defaultValue). If you don't need to add a
 * custom property to your attribute, the code can be stripped to it's minimal
 */
export class ConcreteTessaAttribute extends ConcreteAttribute implements TessaAttribute {
  /**
   * Here, our constructor is private to be sure that our model will be created through a named constructor
   */
  private constructor(
    identifier: Identifier,
    referenceEntityIdentifier: ReferenceEntityIdentifier,
    code: AttributeCode,
    labelCollection: LabelCollection,
    valuePerLocale: boolean,
    valuePerChannel: boolean,
    order: number,
    is_required: boolean,
    readonly maxAssets: TessaMaxAssets,
    readonly allowedExtensions: AllowedExtensions,
    readonly maxDisplayedAssets: TessaMaxDisplayedAssets,
    readonly canEditAssetsInAkeneoUi: boolean
  ) {
    super(
      identifier,
      referenceEntityIdentifier,
      code,
      labelCollection,
      'tessa',
      valuePerLocale,
      valuePerChannel,
      order,
      is_required
    );

    if (!(maxAssets instanceof TessaMaxAssets)) {
      throw new InvalidArgumentError('Attribute expects a TessaMaxAssets as maxAssets')
    }

    if (!(allowedExtensions instanceof AllowedExtensions)) {
      throw new InvalidArgumentError('Attribute expects a AllowedExtension as allowedExtension');
    }

    if (!(maxDisplayedAssets instanceof TessaMaxDisplayedAssets)) {
      throw new InvalidArgumentError('Attribute expects a TessaMaxDisplayedAssets as maxDisplayedAssets')
    }

    /**
     * This will ensure that your model is not modified after it's creation (see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/freeze)
     */
    Object.freeze(this);
  }

  /**
   * Here, we denormalize our attribute
   */
  public static createFromNormalized(normalizedTessaAttribute: NormalizedTessaAttribute) {
    return new ConcreteTessaAttribute(
      createIdentifier(normalizedTessaAttribute.identifier),
      createReferenceEntityIdentifier(normalizedTessaAttribute.reference_entity_identifier),
      createCode(normalizedTessaAttribute.code),
      createLabelCollection(normalizedTessaAttribute.labels),
      normalizedTessaAttribute.value_per_locale,
      normalizedTessaAttribute.value_per_channel,
      normalizedTessaAttribute.order,
      normalizedTessaAttribute.is_required,
      TessaMaxAssets.createFromNormalized(normalizedTessaAttribute.max_assets),
      AllowedExtensions.createFromNormalized(normalizedTessaAttribute.allowed_extensions),
      TessaMaxDisplayedAssets.createFromNormalized(normalizedTessaAttribute.max_displayed_assets),
      normalizedTessaAttribute.canEditAssetsInAkeneoUi
    );
  }

  /**
   * The only method to implement here: the normalize method. Here you need to provide a serializable object (see https://developer.mozilla.org/en-US/docs/Glossary/Serialization)
   */
  public normalize(): NormalizedTessaAttribute {
    return {
      ...super.normalize(),
      type: 'tessa',
      max_assets: this.maxAssets.normalize(),
      allowed_extensions: this.allowedExtensions.normalize(),
      max_displayed_assets: this.maxDisplayedAssets.normalize(),
      canEditAssetsInAkeneoUi: this.canEditAssetsInAkeneoUi
    };
  }
}

/**
 * The only required part of the file: exporting a denormalize method returning a custom attribute implementing Attribute interface
 */
export const denormalize = ConcreteTessaAttribute.createFromNormalized;

/**
 * Our custom attribute reducer needs to receive the normalized custom attribute as input, the code of the additional property and the value of the additional property.
 * It returns the normalized custom attribute.
 */
const tessaAttributeReducer = (
  normalizedAttribute: NormalizedTessaAttribute,
  propertyCode: string,
  propertyValue: NormalizedTessaAdditionalProperty
): NormalizedTessaAttribute => {
  switch (propertyCode) {
    case 'max_assets':
      const max_assets = propertyValue as NormalizedTessaMaxAssets;
      return {...normalizedAttribute, max_assets};
    case 'allowed_extensions':
      const allowed_extensions = propertyValue as NormalizedAllowedExtensions;
      return {...normalizedAttribute, allowed_extensions};
    case 'max_displayed_assets':
      const max_displayed_assets = propertyValue as NormalizedTessaMaxDisplayedAssets;
      return {...normalizedAttribute, max_displayed_assets};

    default:
      break;
  }

  return normalizedAttribute;
};

/**
 * The only required part of the file: exporting the custom attribute reducer.
 * Be aware that the export has to be named ``reducer``
 */
export const reducer = tessaAttributeReducer;

const TessaAttributeView = ({attribute, onAdditionalPropertyUpdated, onSubmit, errors, rights}: {
  attribute: TessaAttribute;
  onAdditionalPropertyUpdated: (property: string, value: TessaAdditionalProperty) => void;
  onSubmit: () => void;
  errors: ValidationError[];
  rights: {
    attribute: {
      create: boolean;
      edit: boolean;
      delete: boolean;
    };
  }
}) => {
  const inputTextClassName = `AknTextField AknTextField--light ${
    !rights.attribute.edit ? 'AknTextField--disabled' : ''
    }`;

  return (
    <React.Fragment>
      <div className="AknFieldContainer" data-code="maxAssets">
        <div className="AknFieldContainer-header AknFieldContainer-header--light">
          <label className="AknFieldContainer-label"
                 htmlFor="pim_reference_entity.attribute.edit.input.tessa.max_assets">
            {__('pim_reference_entity.attribute.edit.input.tessa.max_assets')}
          </label>
        </div>
        <div className="AknFieldContainer-inputContainer">
          <input
            type="text"
            autoComplete="off"
            className={inputTextClassName}
            id="pim_reference_entity.attribute.edit.input.tessa.max_assets"
            maxLength={11}
            name="max_assets"
            readOnly={!rights.attribute.edit}
            value={attribute.maxAssets.stringValue()}
            onKeyPress={(event: React.KeyboardEvent<HTMLInputElement>) => {
              if (Key.Enter === event.key) onSubmit();
            }}
            onChange={(event: React.FormEvent<HTMLInputElement>) => {
              if (!TessaMaxAssets.isValid(event.currentTarget.value)) {
                event.currentTarget.value = attribute.maxAssets.stringValue();
                event.preventDefault();
                return;
              }
              onAdditionalPropertyUpdated('max_assets', TessaMaxAssets.createFromString(event.currentTarget.value));
            }}
          />
        </div>
        {getErrorsView(errors, 'maxAssets')}
      </div>
      <div className="AknFieldContainer" data-code="maxDisplayedAssets">
        <div className="AknFieldContainer-header AknFieldContainer-header--light">
          <label className="AknFieldContainer-label"
                 htmlFor="pim_reference_entity.attribute.edit.input.tessa.max_displayed_assets">
            {__('pim_reference_entity.attribute.edit.input.tessa.max_displayed_assets')}
          </label>
        </div>
        <div className="AknFieldContainer-inputContainer">
          <input
            type="text"
            autoComplete="off"
            className={inputTextClassName}
            id="pim_reference_entity.attribute.edit.input.tessa.max_displayed_assets"
            maxLength={11}
            name="max_displayed_assets"
            readOnly={!rights.attribute.edit}
            value={attribute.maxDisplayedAssets.stringValue()}
            onKeyPress={(event: React.KeyboardEvent<HTMLInputElement>) => {
              if (Key.Enter === event.key) onSubmit();
            }}
            onChange={(event: React.FormEvent<HTMLInputElement>) => {
              if (!TessaMaxDisplayedAssets.isValid(event.currentTarget.value)) {
                event.currentTarget.value = attribute.maxDisplayedAssets.stringValue();
                event.preventDefault();
                return;
              }
              onAdditionalPropertyUpdated('max_displayed_assets', TessaMaxDisplayedAssets.createFromString(event.currentTarget.value));
            }}
          />
        </div>
        {getErrorsView(errors, 'maxDisplayedAssets')}
      </div>
      <div className="AknFieldContainer" data-code="allowedExtensions">
        <div className="AknFieldContainer-header AknFieldContainer-header--light">
          <label
            className="AknFieldContainer-label"
            htmlFor="pim_reference_entity.attribute.edit.input.allowed_extensions"
          >
            {__('pim_reference_entity.attribute.edit.input.allowed_extensions')}
          </label>
        </div>
        <div className="AknFieldContainer-inputContainer">
          <Select2
            id="pim_reference_entity.attribute.edit.input.allowed_extensions"
            name="allowed_extensions"
            data={(AllowedExtensionsOptions as any) as {[choiceValue: string]: string}}
            value={attribute.allowedExtensions.arrayValue()}
            multiple={true}
            readOnly={!rights.attribute.edit}
            configuration={{
              allowClear: true,
            }}
            onChange={(allowedExtensions: string[]) => {
              onAdditionalPropertyUpdated('allowed_extensions', AllowedExtensions.createFromArray(allowedExtensions));
            }}
          />
        </div>
        {getErrorsView(errors, 'allowedExtensions')}
      </div>
    </React.Fragment>
  );
};

/**
 * The only required part of the file: exporting the custom attribute view. Note that the export name has to be ``view``
 */
export const view = TessaAttributeView;
