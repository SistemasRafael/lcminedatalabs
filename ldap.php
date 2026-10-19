<?php

require_once("config.php");

function ActiveDirectoryAuthentication($user, $pass)
{
    $ldapconn = ldap_connect("ldap://SA-DC-HSTR01.hstr.local:389"); 
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    //FORMATO CORRECTO
    $ldaprdn   = 'HSTR\\' . trim($user); // ej: rafael.flores
    $ldappass = trim($pass);

    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    if (!$ldapbind) {
        echo "LDAP ERROR: " . ldap_error($ldapconn);
        ldap_close($ldapconn);
        return false;
    }
    
    ldap_close($ldapconn);
    return true;
}

function GetUserInformation($user, $pass)
{
    $data = array(
        'cuenta' => null,
        'nombre' => null,
        'last'   => null,
        'correo' => null,
        'member' => null
    );

    $ldapconn = ldap_connect("ldap://SA-DC-HSTR01.hstr.local:389"); 
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

    $ldaprdn = 'HSTR\\' . trim($user);
    $ldappass = trim($pass);

    $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);

    if (!$ldapbind) {
        ldap_close($ldapconn);
        return $data;
    }

    $base_dn = "DC=hstr,DC=local";
    $filter = "(samaccountname=" . trim($user) . ")";
    $attributes = array("samaccountname", "givenname", "sn", "mail", "memberof");

    $result = ldap_search($ldapconn, $base_dn, $filter, $attributes);

    if ($result) {
        $info = ldap_get_entries($ldapconn, $result);

        if ($info["count"] > 0) {
            $data['cuenta'] = $info[0]["samaccountname"][0] ?? null;
            $data['nombre'] = $info[0]["givenname"][0] ?? null;
            $data['last']   = $info[0]["sn"][0] ?? null;
            $data['correo'] = $info[0]["mail"][0] ?? null;
            $data['member'] = $info[0]["memberof"][0] ?? null;
        }
    }

    ldap_close($ldapconn);
    return $data;
}

function mailboxpowerloginrd($user,$pass){
	 $ldapconn = ldap_connect("ldaps://dc01.hstr.local:636");

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

$ldaprdn = trim($user) . '@hstr.local';
$ldappass = trim($pass);

$bind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

if (!$bind) {
    echo ldap_error($ldapconn);
}    
	 echo "Todo perfecto = ".$bind;
} 
?>
