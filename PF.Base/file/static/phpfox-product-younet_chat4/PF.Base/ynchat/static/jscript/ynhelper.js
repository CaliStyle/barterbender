;
var ynhelper = {
	pt : []
	, base64_encode: function(data)
	{	
	  //  discuss at: http://phpjs.org/functions/base64_encode/
	  // original by: Tyler Akins (http://rumkin.com)
	  // improved by: Bayron Guevara
	  // improved by: Thunder.m
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: Rafał Kukawski (http://kukawski.pl)
	  // bugfixed by: Pellentesque Malesuada
	  //   example 1: base64_encode('Kevin van Zonneveld');
	  //   returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
	  //   example 2: base64_encode('a');
	  //   returns 2: 'YQ=='
	
	  var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
	  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
	    ac = 0,
	    enc = '',
	    tmp_arr = [];
	
	  if (!data) {
	    return data;
	  }
	
	  do { // pack three octets into four hexets
	    o1 = data.charCodeAt(i++);
	    o2 = data.charCodeAt(i++);
	    o3 = data.charCodeAt(i++);
	
	    bits = o1 << 16 | o2 << 8 | o3;
	
	    h1 = bits >> 18 & 0x3f;
	    h2 = bits >> 12 & 0x3f;
	    h3 = bits >> 6 & 0x3f;
	    h4 = bits & 0x3f;
	
	    // use hexets to index into b64, and append result to encoded string
	    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
	  } while (i < data.length);
	
	  enc = tmp_arr.join('');
	
	  var r = data.length % 3;
	
	  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);		
	}
    , base64_decode: function(data)
    {
          //  discuss at: http://phpjs.org/functions/base64_decode/
          // original by: Tyler Akins (http://rumkin.com)
          // improved by: Thunder.m
          // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          //    input by: Aman Gupta
          //    input by: Brett Zamir (http://brett-zamir.me)
          // bugfixed by: Onno Marsman
          // bugfixed by: Pellentesque Malesuada
          // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          //   example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
          //   returns 1: 'Kevin van Zonneveld'
          //   example 2: base64_decode('YQ===');
          //   returns 2: 'a'

          var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
          var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            dec = '',
            tmp_arr = [];

          if (!data) {
            return data;
          }

          data += '';

          do { // unpack four hexets into three octets using index points in b64
            h1 = b64.indexOf(data.charAt(i++));
            h2 = b64.indexOf(data.charAt(i++));
            h3 = b64.indexOf(data.charAt(i++));
            h4 = b64.indexOf(data.charAt(i++));

            bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;

            o1 = bits >> 16 & 0xff;
            o2 = bits >> 8 & 0xff;
            o3 = bits & 0xff;

            if (h3 == 64) {
              tmp_arr[ac++] = String.fromCharCode(o1);
            } else if (h4 == 64) {
              tmp_arr[ac++] = String.fromCharCode(o1, o2);
            } else {
              tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
            }
          } while (i < data.length);

          dec = tmp_arr.join('');

          return dec.replace(/\0+$/, '');
    }
    , bytesToSize: function(bytes)
    {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Bytes';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
    , nl2br: function(str, is_xhtml)
    {
          //  discuss at: http://phpjs.org/functions/nl2br/
          // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          // improved by: Philip Peterson
          // improved by: Onno Marsman
          // improved by: Atli Þór
          // improved by: Brett Zamir (http://brett-zamir.me)
          // improved by: Maximusya
          // bugfixed by: Onno Marsman
          // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          //    input by: Brett Zamir (http://brett-zamir.me)
          //   example 1: nl2br('Kevin\nvan\nZonneveld');
          //   returns 1: 'Kevin<br />\nvan<br />\nZonneveld'
          //   example 2: nl2br("\nOne\nTwo\n\nThree\n", false);
          //   returns 2: '<br>\nOne<br>\nTwo<br>\n<br>\nThree<br>\n'
          //   example 3: nl2br("\nOne\nTwo\n\nThree\n", true);
          //   returns 3: '<br />\nOne<br />\nTwo<br />\n<br />\nThree<br />\n'

          var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

          return (str + '')
            .replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }
    , htmlspecialchars: function(string, quote_style, charset, double_encode)
    {
          //       discuss at: http://phpjs.org/functions/htmlspecialchars/
          //      original by: Mirek Slugen
          //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          //      bugfixed by: Nathan
          //      bugfixed by: Arno
          //      bugfixed by: Brett Zamir (http://brett-zamir.me)
          //      bugfixed by: Brett Zamir (http://brett-zamir.me)
          //       revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
          //         input by: Ratheous
          //         input by: Mailfaker (http://www.weedem.fr/)
          //         input by: felix
          // reimplemented by: Brett Zamir (http://brett-zamir.me)
          //             note: charset argument not supported
          //        example 1: htmlspecialchars("<a href='test'>Test</a>", 'ENT_QUOTES');
          //        returns 1: '&lt;a href=&#039;test&#039;&gt;Test&lt;/a&gt;'
          //        example 2: htmlspecialchars("ab\"c'd", ['ENT_NOQUOTES', 'ENT_QUOTES']);
          //        returns 2: 'ab"c&#039;d'
          //        example 3: htmlspecialchars('my "&entity;" is still here', null, null, false);
          //        returns 3: 'my &quot;&entity;&quot; is still here'

          var optTemp = 0,
            i = 0,
            noquotes = false;
          if (typeof quote_style === 'undefined' || quote_style === null) {
            quote_style = 2;
          }
          string = string.toString();
          if (double_encode !== false) { // Put this first to avoid double-encoding
            string = string.replace(/&/g, '&amp;');
          }
          string = string.replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

          var OPTS = {
            'ENT_NOQUOTES': 0,
            'ENT_HTML_QUOTE_SINGLE': 1,
            'ENT_HTML_QUOTE_DOUBLE': 2,
            'ENT_COMPAT': 2,
            'ENT_QUOTES': 3,
            'ENT_IGNORE': 4
          };
          if (quote_style === 0) {
            noquotes = true;
          }
          if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
            quote_style = [].concat(quote_style);
            for (i = 0; i < quote_style.length; i++) {
              // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
              if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
              } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
              }
            }
            quote_style = optTemp;
          }
          if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/'/g, '&#039;');
          }
          if (!noquotes) {
            string = string.replace(/"/g, '&quot;');
          }

          return string;
    }
};  

