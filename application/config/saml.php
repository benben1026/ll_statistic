<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Your website location or in saml term your service provider location
$sp_host = 'http://dash.keep.edu.hk/index.php';

//KEEP's identity provider location
$idp_host = 'https://account.keep.edu.hk/idp';

$config['saml'] = array(
	/*****
 	* One Loign Settings
 	*/
	// If 'strict' is True, then the PHP Toolkit will reject unsigned
	// or unencrypted messages if it expects them signed or encrypted
	// Also will reject the messages if not strictly follow the SAML
	// standard: Destination, NameId, Conditions ... are validated too.
	'strict' => false,
	// Enable debug mode (to print errors)
	'debug' => false,
	// Service Provider Data that we are deploying
	'sp' => array(
    	//LARAVEL - You don't need to change anything else on the sp
    	// Identifier of the SP entity  (must be a URI)
    	'entityId' => $sp_host.'/saml2Controller/metadata',
    	// Specifies info about where and how the <AuthnResponse> message MUST be
    	// returned to the requester, in this case our SP.
    	'assertionConsumerService' => array(
        	// URL Location where the <Response> from the IdP will be returned
        	'url' => $sp_host.'/saml2Controller/acs',
        	// SAML protocol binding to be used when returning the <Response>
        	// message.  Onelogin Toolkit supports for this endpoint the
        	// HTTP-Redirect binding only
        	'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
    	),
    	// Specifies info about where and how the <Logout Response> message MUST be
    	// returned to the requester, in this case our SP.
    	'singleLogoutService' => array(
        	// URL Location where the <Response> from the IdP will be returned
        	'url' => $sp_host.'/saml2Controller/logout',
        	// SAML protocol binding to be used when returning the <Response>
        	// message.  Onelogin Toolkit supports for this endpoint the
        	// HTTP-Redirect binding only
        	'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    	),
    	// Specifies constraints on the name identifier to be used to
    	// represent the requested subject.
    	// Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
    	'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
    	// Usually x509cert and privateKey of the SP are provided by files placed at
    	// the certs folder. But we can also provide them with the following parameters
    	'x509cert' => '',
    	'privateKey' => '',
	),
	// Identity Provider Data that we want connect with our SP
	'idp' => array(
    	// Identifier of the IdP entity  (must be a URI)
    	'entityId' => $idp_host . '/saml2/idp/metadata.php',
    	// SSO endpoint info of the IdP. (Authentication Request protocol)
    	'singleSignOnService' => array(
        	// URL Target of the IdP where the SP will send the Authentication Request Message
        	'url' => $idp_host . '/saml2/idp/SSOService.php',
        	// SAML protocol binding to be used when returning the <Response>
        	// message.  Onelogin Toolkit supports for this endpoint the
        	// HTTP-POST binding only
        	'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    	),
    	// SLO endpoint info of the IdP.
    	'singleLogoutService' => array(
        	// URL Location of the IdP where the SP will send the SLO Request
        	'url' => $idp_host . '/saml2/idp/SingleLogoutService.php',
        	// SAML protocol binding to be used when returning the <Response>
        	// message.  Onelogin Toolkit supports for this endpoint the
        	// HTTP-Redirect binding only
        	'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
    	),
    	// Public x509 certificate of the IdP
    	// This cert valid for both staging and production environment
    	'x509cert' => 'MIIFIjCCBAqgAwIBAgIJAMnfIpugoOveMA0GCSqGSIb3DQEBCwUAMIG0MQswCQYDVQQGEwJVUzEQMA4GA1UECBMHQXJpem9uYTETMBEGA1UEBxMKU2NvdHRzZGFsZTEaMBgGA1UEChMRR29EYWRkeS5jb20sIEluYy4xLTArBgNVBAsTJGh0dHA6Ly9jZXJ0cy5nb2RhZGR5LmNvbS9yZXBvc2l0b3J5LzEzMDEGA1UEAxMqR28gRGFkZHkgU2VjdXJlIENlcnRpZmljYXRlIEF1dGhvcml0eSAtIEcyMB4XDTE1MDEwOTA4NDAzOFoXDTE4MDEwOTA4NDAzOFowOzEhMB8GA1UECxMYRG9tYWluIENvbnRyb2wgVmFsaWRhdGVkMRYwFAYDVQQDDA0qLmtlZXAuZWR1LmhrMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyipuWH0bA7XDkgskVCE8csDPLz5SiZoot5eHETJyATqJfrZpq3pFUOw7uH/V9pGL+2kHsg6LuD0oyQhH6TK5w5I2ph0k/+PB0GpxST4BJrQgwvxx9R/aYfdxLDc/fi3rM1Ur8pZz0wH3/G6APeI7zDa3nuNOisRzs/+aE7t3Z6Sh6SxcHrqo71fMfmWGhLpt9eR6cryOStWjxb28cQF0JdFnbclu4OFS81VQ0d6luhN5e75QZKDMdJSYLpl6U/YwAEbKUkt5UUuMGJchKdpu1N+tLXRzN4uRdr3hJOmzldf4puB6sWQrdJXGckqrPpLqIrSv4onICnizcn2yvOCQ0wIDAQABo4IBrTCCAakwDAYDVR0TAQH/BAIwADAdBgNVHSUEFjAUBggrBgEFBQcDAQYIKwYBBQUHAwIwDgYDVR0PAQH/BAQDAgWgMDYGA1UdHwQvMC0wK6ApoCeGJWh0dHA6Ly9jcmwuZ29kYWRkeS5jb20vZ2RpZzJzMS04Ny5jcmwwUwYDVR0gBEwwSjBIBgtghkgBhv1tAQcXATA5MDcGCCsGAQUFBwIBFitodHRwOi8vY2VydGlmaWNhdGVzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvMHYGCCsGAQUFBwEBBGowaDAkBggrBgEFBQcwAYYYaHR0cDovL29jc3AuZ29kYWRkeS5jb20vMEAGCCsGAQUFBzAChjRodHRwOi8vY2VydGlmaWNhdGVzLmdvZGFkZHkuY29tL3JlcG9zaXRvcnkvZ2RpZzIuY3J0MB8GA1UdIwQYMBaAFEDCvSeOzDSDMKIz1/tss/C0LIDOMCUGA1UdEQQeMByCDSoua2VlcC5lZHUuaGuCC2tlZXAuZWR1LmhrMB0GA1UdDgQWBBTcfn33jUlBdj1zzp9MJ8pEPMJl1TANBgkqhkiG9w0BAQsFAAOCAQEAjA3+lOJQqUXnZ2wheAvk6mw+lliW44JwAUxQoq6XIbJZ6ErExkNW0iyVGVNDz6IB92goJoOFDkgUFhzn65xKODk1u7E84ydjHma4wIF+5XxeHptphXmxNlHr/x63WzaNPH/10VUfJo8j8MWbgW8Je4iH8mPK8kcHhPIsWj4OTd9+9WDkXE8p47wW6XjMRp0g5/Y1m7LD3ES2NeH6OlEt4faguC1kGKi3YWURx42sgJ8yy2gtqVGfnOwYRm/Oql+zD4y2xFo6YnBC2Ya/SmLnrev/t7z9jA4VdKmb2kzFJdtaUxYi65Gwl8HvD8WHjYK1o/grwawS/U2U4Xm6Ij0V4Q==',
    	/*
     	*  Instead of use the whole x509cert you can use a fingerprint
     	*  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
     	*/
    	// 'certFingerprint' => '',
	),

);
