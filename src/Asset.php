<?php

namespace VoightKampff;

class Asset extends \Aetiom\PhpUtils\Asset
{
    /**
     * Fetch data
     * 
     * @param mixed $subKey : data subKey to extract (can be null)
     * @return mixed : extracted data, can be an array
     * 
     * @throws \Exception if subKey does not exist
     */
    public function fetch($subKey = null)
    {
        try {
            return parent::fetch($subKey);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERR_ASSET_KEY_UNKNOWN && !is_array($this->asset)) {
                return $this->asset;
            }

            throw $e;
        }
    }
}