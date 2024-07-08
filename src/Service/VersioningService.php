<?php

// Creating a Symfony service to retrieve the version contained in the "accept" field of the HTTP request.
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class VersioningService
{
    private $requestStack;
    private $defaultVersion;

    /**
     * Constructor for retrieving the current request (to extract the "accept" field from the header)
     * as well as the ParameterBagInterface to retrieve the default version from the configuration file
     *
     * @param RequestStack $requestStack
     * @param ParameterBagInterface $params
     */
    public function __construct(RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->requestStack = $requestStack;
        $this->defaultVersion = $params->get('default_api_version');
    }

    /**
     * Retrieving the version that was sent in the "accept" header of the HTTP request
     *
     * @return string : the version number. By default, the returned version is the one defined in the configuration file services.yaml: "default_api_version"
     */
    public function getVersion(): string
    {
        $version = $this->defaultVersion;

        $request = $this->requestStack->getCurrentRequest();
        $accept = $request->headers->get('Accept');
        // Retrieving the version number from the accept string:
        // example "application/json; test=thing; version=2.0" => 2.0
        $entete = explode(';', $accept);

        // We go through all the headers to find the version
        foreach ($entete as $value) {
            if (strpos($value, 'version') !== false) {
                $version = explode('=', $value);
                $version = $version[1];
                break;
            }
        }
        return $version;
    }
}
