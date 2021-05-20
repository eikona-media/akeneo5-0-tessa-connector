<?php
    /**
     * Math.php
     *
     * @author    Matthias Mahler <m.mahler@eikona.de>
     * @copyright 2017 Eikona AG (http://www.eikona.de)
     */

    namespace Eikona\Tessa\ConnectorBundle\Utilities;

    class Math
    {
        /**
         * @param $value string
         * @return int
         */
        public function getCrossSum($value)
        {
            return array_sum(str_split($value));
        }
    }
