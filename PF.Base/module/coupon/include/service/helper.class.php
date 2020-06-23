<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */

class Coupon_Service_Helper extends Phpfox_Service
{
	public function pushMailToQueue($iCouponId, $sEmailType, $aReceivers = array(), $iClaimerId = 0)
	{
		$aEmail = Phpfox::getService('coupon.mail')->getEmailMessageFromTemplate(Phpfox::getService('coupon.mail')->getTypesCode($sEmailType), $iCouponId, 0, $iClaimerId);
		
		$aInsert = array(
			'coupon_id' => $iCouponId,
			'receivers'	=> serialize($aReceivers),
			'email_subject'	=> $aEmail['subject'],
			'time_stamp'=>PHPFOX_TIME,
			'email_message'=>$aEmail['message'],
			'is_sent'	=> 0,
			'is_site_user'	=> 1
		);
		Phpfox::getService('coupon.mail.process')->saveEmailToQueue($aInsert);
	}
    
    public function getSelectCountriesForSearch($sSelected = null)
    {
        $sContries = '<select class="form-control" name="search[country_iso]" id="country_iso">' . "\n";
		$sContries .= "\t\t" . '<option value="-1"'.((isset($sSelected) && $sSelected == '-1') ? ' selected="selected"' : '').'>' . _p('core.select') . ':</option>' . "\n";
		
        foreach (Phpfox::getService('core.country')->get() as $sIso => $sCountry)
		{
            $sContries .= "\t\t\t" . '<option class="js_country_option" id="js_country_iso_option_' . $sIso . '" value="' . $sIso . '"' . ((isset($sSelected) && $sSelected == $sIso) ? ' selected="selected"' : '') . ' >' . (Phpfox::getLib('locale')->isPhrase('core.translate_country_iso_' . strtolower($sIso)) ? _p('core.translate_country_iso_' . strtolower($sIso)) : '') . str_replace("'", "\'", $sCountry) . '</option>' . "\n";
		}
		
        $sContries .= "\t\t" . '</select>';
		return $sContries;
    }


    
    private $_aCurrency = array(
        'AED' => array(
            'name' => 'United Arab Emirates Dirham',
            'symbol' => NULL
        ),
        'AFN' => array(
            'name' => 'Afghanistan Afghani',
            'symbol' => '؋'
        ),
        'ALL' => array(
            'name' => 'Albania Lek',
            'symbol' => 'Lek'
        ),
        'AMD' => array(
            'name' => 'Armenia Dram',
            'symbol' => NULL
        ),
        'ANG' => array(
            'name' => 'Netherlands Antilles Guilder',
            'symbol' => 'ƒ'
        ),
        'AOA' => array(
            'name' => 'Angola Kwanza',
            'symbol' => NULL
        ),
        'ARS' => array(
            'name' => 'Argentina Peso',
            'symbol' => '$'
        ),
        'AUD' => array(
            'name' => 'Australia Dollar',
            'symbol' => '$'
        ),
        'AWG' => array(
            'name' => 'Aruba Guilder',
            'symbol' => 'ƒ'
        ),
        'AZN' => array(
            'name' => 'Azerbaijan New Manat',
            'symbol' => 'ман'
        ),
        'BAM' => array(
            'name' => 'Bosnia and Herzegovina Convertible Marka',
            'symbol' => 'KM'
        ),
        'BBD' => array(
            'name' => 'Barbados Dollar',
            'symbol' => '$'
        ),
        'BDT' => array(
            'name' => 'Bangladesh Taka',
            'symbol' => NULL
        ),
        'BGN' => array(
            'name' => 'Bulgaria Lev',
            'symbol' => 'лв'
        ),
        'BHD' => array(
            'name' => 'Bahrain Dinar',
            'symbol' => NULL
        ),
        'BIF' => array(
            'name' => 'Burundi Franc',
            'symbol' => NULL
        ),
        'BMD' => array(
            'name' => 'Bermuda Dollar',
            'symbol' => '$'
        ),
        'BND' => array(
            'name' => 'Brunei Darussalam Dollar',
            'symbol' => '$'
        ),
        'BOB' => array(
            'name' => 'Bolivia Boliviano',
            'symbol' => '$b'
        ),
        'BRL' => array(
            'name' => 'Brazil Real',
            'symbol' => 'R$'
        ),
        'BSD' => array(
            'name' => 'Bahamas Dollar',
            'symbol' => '$'
        ),
        'BTN' => array(
            'name' => 'Bhutan Ngultrum',
            'symbol' => NULL
        ),
        'BWP' => array(
            'name' => 'Botswana Pula',
            'symbol' => 'P'
        ),
        'BYR' => array(
            'name' => 'Belarus Ruble',
            'symbol' => 'p.'
        ),
        'BZD' => array(
            'name' => 'Belize Dollar',
            'symbol' => 'BZ$'
        ),
        'CAD' => array(
            'name' => 'Canada Dollar',
            'symbol' => '$'
        ),
        'CDF' => array(
            'name' => 'Congo/Kinshasa Franc',
            'symbol' => NULL
        ),
        'CHF' => array(
            'name' => 'Switzerland Franc',
            'symbol' => 'CHF'
        ),
        'CLP' => array(
            'name' => 'Chile Peso',
            'symbol' => '$'
        ),
        'CNY' => array(
            'name' => 'China Yuan Renminbi',
            'symbol' => '¥'
        ),
        'COP' => array(
            'name' => 'Colombia Peso',
            'symbol' => '$'
        ),
        'CRC' => array(
            'name' => 'Costa Rica Colon',
            'symbol' => '¢'
        ),
        'CUC' => array(
            'name' => 'Cuba Convertible Peso',
            'symbol' => NULL
        ),
        'CUP' => array(
            'name' => 'Cuba Peso',
            'symbol' => '₱'
        ),
        'CVE' => array(
            'name' => 'Cape Verde Escudo',
            'symbol' => NULL
        ),
        'CZK' => array(
            'name' => 'Czech Republic Koruna',
            'symbol' => 'Kc'
        ),
        'DJF' => array(
            'name' => 'Djibouti Franc',
            'symbol' => NULL
        ),
        'DKK' => array(
            'name' => 'Denmark Krone',
            'symbol' => 'kr'
        ),
        'DOP' => array(
            'name' => 'Dominican Republic Peso',
            'symbol' => 'RD$'
        ),
        'DZD' => array(
            'name' => 'Algeria Dinar',
            'symbol' => NULL
        ),
        'EGP' => array(
            'name' => 'Egypt Pound',
            'symbol' => '£'
        ),
        'ERN' => array(
            'name' => 'Eritrea Nakfa',
            'symbol' => NULL
        ),
        'ETB' => array(
            'name' => 'Ethiopia Birr',
            'symbol' => NULL
        ),
        'EUR' => array(
            'name' => 'Euro Member Countries',
            'symbol' => '€'
        ),
        'FJD' => array(
            'name' => 'Fiji Dollar',
            'symbol' => '$'
        ),
        'FKP' => array(
            'name' => 'Falkland Islands (Malvinas) Pound',
            'symbol' => '£'
        ),
        'GBP' => array(
            'name' => 'United Kingdom Pound',
            'symbol' => '£'
        ),
        'GEL' => array(
            'name' => 'Georgia Lari',
            'symbol' => NULL
        ),
        'GGP' => array(
            'name' => 'Guernsey Pound',
            'symbol' => '£'
        ),
        'GHS' => array(
            'name' => 'Ghana Cedi',
            'symbol' => NULL
        ),
        'GIP' => array(
            'name' => 'Gibraltar Pound',
            'symbol' => '£'
        ),
        'GMD' => array(
            'name' => 'Gambia Dalasi',
            'symbol' => NULL
        ),
        'GNF' => array(
            'name' => 'Guinea Franc',
            'symbol' => NULL
        ),
        'GTQ' => array(
            'name' => 'Guatemala Quetzal',
            'symbol' => 'Q'
        ),
        'GYD' => array(
            'name' => 'Guyana Dollar',
            'symbol' => '$'
        ),
        'HKD' => array(
            'name' => 'Hong Kong Dollar',
            'symbol' => '$'
        ),
        'HNL' => array(
            'name' => 'Honduras Lempira',
            'symbol' => 'L'
        ),
        'HRK' => array(
            'name' => 'Croatia Kuna',
            'symbol' => 'kn'
        ),
        'HTG' => array(
            'name' => 'Haiti Gourde',
            'symbol' => NULL
        ),
        'HUF' => array(
            'name' => 'Hungary Forint',
            'symbol' => 'Ft'
        ),
        'IDR' => array(
            'name' => 'Indonesia Rupiah',
            'symbol' => 'Rp'
        ),
        'ILS' => array(
            'name' => 'Israel Shekel',
            'symbol' => '₪'
        ),
        'IMP' => array(
            'name' => 'Isle of Man Pound',
            'symbol' => '£'
        ),
        'INR' => array(
            'name' => 'India Rupee',
            'symbol' => NULL
        ),
        'IQD' => array(
            'name' => 'Iraq Dinar',
            'symbol' => NULL
        ),
        'IRR' => array(
            'name' => 'Iran Rial',
            'symbol' => '﷼'
        ),
        'ISK' => array(
            'name' => 'Iceland Krona',
            'symbol' => 'kr'
        ),
        'JEP' => array(
            'name' => 'Jersey Pound',
            'symbol' => '£'
        ),
        'JMD' => array(
            'name' => 'Jamaica Dollar',
            'symbol' => 'J$'
        ),
        'JOD' => array(
            'name' => 'Jordan Dinar',
            'symbol' => NULL
        ),
        'JPY' => array(
            'name' => 'Japan Yen',
            'symbol' => '¥'
        ),
        'KES' => array(
            'name' => 'Kenya Shilling',
            'symbol' => NULL
        ),
        'KGS' => array(
            'name' => 'Kyrgyzstan Som',
            'symbol' => 'лв'
        ),
        'KHR' => array(
            'name' => 'Cambodia Riel',
            'symbol' => '៛'
        ),
        'KMF' => array(
            'name' => 'Comoros Franc',
            'symbol' => NULL
        ),
        'KPW' => array(
            'name' => 'Korea (North) Won',
            'symbol' => '₩'
        ),
        'KRW' => array(
            'name' => 'Korea (South) Won',
            'symbol' => '₩'
        ),
        'KWD' => array(
            'name' => 'Kuwait Dinar',
            'symbol' => NULL
        ),
        'KYD' => array(
            'name' => 'Cayman Islands Dollar',
            'symbol' => '$'
        ),
        'KZT' => array(
            'name' => 'Kazakhstan Tenge',
            'symbol' => 'лв'
        ),
        'LAK' => array(
            'name' => 'Laos Kip',
            'symbol' => '₭'
        ),
        'LBP' => array(
            'name' => 'Lebanon Pound',
            'symbol' => '£'
        ),
        'LKR' => array(
            'name' => 'Sri Lanka Rupee',
            'symbol' => '₨'
        ),
        'LRD' => array(
            'name' => 'Liberia Dollar',
            'symbol' => '$'
        ),
        'LSL' => array(
            'name' => 'Lesotho Loti',
            'symbol' => NULL
        ),
        'LTL' => array(
            'name' => 'Lithuania Litas',
            'symbol' => 'Lt'
        ),
        'LVL' => array(
            'name' => 'Latvia Lat',
            'symbol' => 'Ls'
        ),
        'LYD' => array(
            'name' => 'Libya Dinar',
            'symbol' => NULL
        ),
        'MAD' => array(
            'name' => 'Morocco Dirham',
            'symbol' => NULL
        ),
        'MDL' => array(
            'name' => 'Moldova Leu',
            'symbol' => NULL
        ),
        'MGA' => array(
            'name' => 'Madagascar Ariary',
            'symbol' => NULL
        ),
        'MKD' => array(
            'name' => 'Macedonia Denar',
            'symbol' => 'ден'
        ),
        'MMK' => array(
            'name' => 'Myanmar (Burma) Kyat',
            'symbol' => NULL
        ),
        'MNT' => array(
            'name' => 'Mongolia Tughrik',
            'symbol' => '₮'
        ),
        'MOP' => array(
            'name' => 'Macau Pataca',
            'symbol' => NULL
        ),
        'MRO' => array(
            'name' => 'Mauritania Ouguiya',
            'symbol' => NULL
        ),
        'MUR' => array(
            'name' => 'Mauritius Rupee',
            'symbol' => '₨'
        ),
        'MVR' => array(
            'name' => 'Maldives (Maldive Islands) Rufiyaa',
            'symbol' => NULL
        ),
        'MWK' => array(
            'name' => 'Malawi Kwacha',
            'symbol' => NULL
        ),
        'MXN' => array(
            'name' => 'Mexico Peso',
            'symbol' => '$'
        ),
        'MYR' => array(
            'name' => 'Malaysia Ringgit',
            'symbol' => 'RM'
        ),
        'MZN' => array(
            'name' => 'Mozambique Metical',
            'symbol' => 'MT'
        ),
        'NAD' => array(
            'name' => 'Namibia Dollar',
            'symbol' => '$'
        ),
        'NGN' => array(
            'name' => 'Nigeria Naira',
            'symbol' => '₦'
        ),
        'NIO' => array(
            'name' => 'Nicaragua Cordoba',
            'symbol' => 'C$'
        ),
        'NOK' => array(
            'name' => 'Norway Krone',
            'symbol' => 'kr'
        ),
        'NPR' => array(
            'name' => 'Nepal Rupee',
            'symbol' => '₨'
        ),
        'NZD' => array(
            'name' => 'New Zealand Dollar',
            'symbol' => '$'
        ),
        'OMR' => array(
            'name' => 'Oman Rial',
            'symbol' => '﷼'
        ),
        'PAB' => array(
            'name' => 'Panama Balboa',
            'symbol' => 'B/.'
        ),
        'PEN' => array(
            'name' => 'Peru Nuevo Sol',
            'symbol' => 'S/.'
        ),
        'PGK' => array(
            'name' => 'Papua New Guinea Kina',
            'symbol' => NULL
        ),
        'PHP' => array(
            'name' => 'Philippines Peso',
            'symbol' => '₱'
        ),
        'PKR' => array(
            'name' => 'Pakistan Rupee',
            'symbol' => '₨'
        ),
        'PLN' => array(
            'name' => 'Poland Zloty',
            'symbol' => 'zl'
        ),
        'PYG' => array(
            'name' => 'Paraguay Guarani',
            'symbol' => 'Gs'
        ),
        'QAR' => array(
            'name' => 'Qatar Riyal',
            'symbol' => '﷼'
        ),
        'RON' => array(
            'name' => 'Romania New Leu',
            'symbol' => 'lei'
        ),
        'RSD' => array(
            'name' => 'Serbia Dinar',
            'symbol' => 'Дин.'
        ),
        'RUB' => array(
            'name' => 'Russia Ruble',
            'symbol' => 'руб'
        ),
        'RWF' => array(
            'name' => 'Rwanda Franc',
            'symbol' => NULL
        ),
        'SAR' => array(
            'name' => 'Saudi Arabia Riyal',
            'symbol' => '﷼'
        ),
        'SBD' => array(
            'name' => 'Solomon Islands Dollar',
            'symbol' => '$'
        ),
        'SCR' => array(
            'name' => 'Seychelles Rupee',
            'symbol' => '₨'
        ),
        'SDG' => array(
            'name' => 'Sudan Pound',
            'symbol' => NULL
        ),
        'SEK' => array(
            'name' => 'Sweden Krona',
            'symbol' => 'kr'
        ),
        'SGD' => array(
            'name' => 'Singapore Dollar',
            'symbol' => '$'
        ),
        'SHP' => array(
            'name' => 'Saint Helena Pound',
            'symbol' => '£'
        ),
        'SLL' => array(
            'name' => 'Sierra Leone Leone',
            'symbol' => NULL
        ),
        'SOS' => array(
            'name' => 'Somalia Shilling',
            'symbol' => 'S'
        ),
        'SPL' => array(
            'name' => 'Seborga Luigino',
            'symbol' => NULL
        ),
        'SRD' => array(
            'name' => 'Suriname Dollar',
            'symbol' => '$'
        ),
        'STD' => array(
            'name' => 'São Tomé and Príncipe Dobra',
            'symbol' => NULL
        ),
        'SVC' => array(
            'name' => 'El Salvador Colon',
            'symbol' => '$'
        ),
        'SYP' => array(
            'name' => 'Syria Pound',
            'symbol' => '£'
        ),
        'SZL' => array(
            'name' => 'Swaziland Lilangeni',
            'symbol' => NULL
        ),
        'THB' => array(
            'name' => 'Thailand Baht',
            'symbol' => '฿'
        ),
        'TJS' => array(
            'name' => 'Tajikistan Somoni',
            'symbol' => NULL
        ),
        'TMT' => array(
            'name' => 'Turkmenistan Manat',
            'symbol' => NULL
        ),
        'TND' => array(
            'name' => 'Tunisia Dinar',
            'symbol' => NULL
        ),
        'TOP' => array(
            'name' => 'Tonga Pa\'anga',
            'symbol' => NULL
        ),
        'TRY' => array(
            'name' => 'Turkey Lira',
            'symbol' => NULL
        ),
        'TTD' => array(
            'name' => 'Trinidad and Tobago Dollar',
            'symbol' => 'TT$'
        ),
        'TVD' => array(
            'name' => 'Tuvalu Dollar',
            'symbol' => '$'
        ),
        'TWD' => array(
            'name' => 'Taiwan New Dollar',
            'symbol' => 'NT$'
        ),
        'TZS' => array(
            'name' => 'Tanzania Shilling',
            'symbol' => NULL
        ),
        'UAH' => array(
            'name' => 'Ukraine Hryvna',
            'symbol' => '₴'
        ),
        'UGX' => array(
            'name' => 'Uganda Shilling',
            'symbol' => NULL
        ),
        'USD' => array(
            'name' => 'United States Dollar',
            'symbol' => '$'
        ),
        'UYU' => array(
            'name' => 'Uruguay Peso',
            'symbol' => '$U'
        ),
        'UZS' => array(
            'name' => 'Uzbekistan Som',
            'symbol' => 'лв'
        ),
        'VEF' => array(
            'name' => 'Venezuela Bolivar',
            'symbol' => 'Bs'
        ),
        'VND' => array(
            'name' => 'Viet Nam Dong',
            'symbol' => '₫'
        ),
        'VUV' => array(
            'name' => 'Vanuatu Vatu',
            'symbol' => NULL
        ),
        'WST' => array(
            'name' => 'Samoa Tala',
            'symbol' => NULL
        ),
        'XAF' => array(
            'name' => 'Communauté Financière Africaine (BEAC) CFA Franc BEAC',
            'symbol' => NULL
        ),
        'XCD' => array(
            'name' => 'East Caribbean Dollar',
            'symbol' => '$'
        ),
        'XDR' => array(
            'name' => 'International Monetary Fund (IMF) Special Drawing Rights',
            'symbol' => NULL
        ),
        'XOF' => array(
            'name' => 'Communauté Financière Africaine (BCEAO) Franc',
            'symbol' => NULL
        ),
        'XPF' => array(
            'name' => 'Comptoirs Français du Pacifique (CFP) Franc',
            'symbol' => NULL
        ),
        'YER' => array(
            'name' => 'Yemen Rial',
            'symbol' => '﷼'
        ),
        'ZAR' => array(
            'name' => 'South Africa Rand',
            'symbol' => 'R'
        ),
        'ZMW' => array(
            'name' => 'Zambia Kwacha',
            'symbol' => NULL
        ),
        'ZWD' => array(
            'name' => 'Zimbabwe Dollar',
            'symbol' => 'Z$'
        )
    );
    
    public function getAllCurrency()
    {
        $aCurrency = $this->_aCurrency;
        
        foreach($aCurrency as $code => $aC)
        {
            $aCurrency[$code]['name'] = $aC['name'];
            $aCurrency[$code]['symbol'] = !empty($aC['symbol']) ? $aC['symbol'] : $code;
        }
        
        return $aCurrency;
    }
    
    public function getCurrencySymbol($sCode)
    {
        $sCode = strtoupper($sCode);
        
        if (!empty($this->_aCurrency[$sCode]['symbol']))
        {
            return $this->_aCurrency[$sCode]['symbol'];
        }
        
        return $sCode;
    }

    public function getDefaultCurrency(){

        $aCurrencyDefault = 'USD';

        $aCurrencies = Phpfox::getService('core.currency')->get();

        foreach ($aCurrencies as $sKey => $aCurrency)
            {
                if ($aCurrency['is_default'] == '1')
                {
                    $aCurrencyDefault = $sKey;
                    break;
                }
            }
        return $aCurrencyDefault;
    }

}
?>