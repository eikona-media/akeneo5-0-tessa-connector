<?php
/**
 * FileNormalizer.php
 *
 * @author    Timo Müller <t.mueller@eikona-media.de>
 * @copyright 2018 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Extension\Normalizer\Standard;

class FileNormalizer extends \Akeneo\Pim\Enrichment\Component\Product\Normalizer\InternalApi\FileNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($file, $format = null, array $context = [])
    {
        if ($this->isTessaFile($file)) {
            $assetId = $this->getTessaAssetId($file);
            $assetFilePath = $assetId !== null
                ? $this->generateTessaFilePath($assetId)
                : null;

            return [
                'filePath' => $assetFilePath,
                'originalFilename' => $assetId
            ];
        }

        return parent::normalize($file, $format, $context);
    }

    /**
     * Gibt zurück, ob ein File ein Tessa-Asset ist
     *
     * @param $file
     * @return bool
     */
    protected function isTessaFile($file)
    {
        /*
        Nur prüfen, ob es ein String mit 0-9 (Asset-IDs) und "," (Delimiter) ist
        Die Prüfung ist ausreichend, da der Normalizer nur für "echte" Bilder und
        das Hauptbild aufgerufen werden kann

        - Für normale Bilder wird die Methode "supportsNormalization" aufgerufen, welche nur bei "echten"
          Bildern true zurück gibt

        - Für das Hauptbild wird die Methode "supportsNormalization" NICHT aufgerufen, deshalb kommen in
          dieser Methode ("isTessaFile") nur "echte" Bilder und Tessa-Attribute (strings) rein

        Hinweis: Referenz-Ids (mit Doppelpunkt getrennt) müssen hier nicht geprüft werden, da es dort kein Hauptbild gibt
        */
        return is_string($file) && preg_match('/^[0-9,]+$/', $file);
    }

    /**
     * Gibt die Tessa-Asset-Id zurück
     *
     * @param $assetIds
     * @return null|string
     */
    protected function getTessaAssetId($assetIds)
    {
        if (empty($assetIds) && $assetIds !== '0') {
            return null;
        }

        // Wenn mehrere Tessa-Assets eingetragen sind, wir das erste verwendet
        return explode(',', $assetIds)[0];
    }

    /**
     * Generiert einen "Pfad" für ein Tessa-Attribut, welcher im Frontend ausgewertet werden kann
     *
     * @param $assetId
     * @return string
     */
    protected function generateTessaFilePath($assetId)
    {
        return '%tessa%_' . $assetId;
    }
}

