/*
 * MailCrypt
 * v.1.0.1
 *
 * Copyright (c) 2018 Pascal Bajorat (https://www.pascal-bajorat.com)
 * Dual licensed under the MIT (below)
 * and GPL (http://www.gnu.org/licenses/gpl.txt) licenses.
 *
 *
 * https://www.pascal-bajorat.com
 * https://www.webdesign-podcast.de

MIT License

Copyright (c) 2018 Pascal Bajorat

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * Use this structure for your mails:
 * ==================================
 *
 * <a href="kontakt.html" class="mailcrypt">info<span><span>∂</span></span>webdesign-podcast.de</a>
 *
 * You can replace the ∂ sign with an @ image or another sign what ever you want.
 *
 */

(function($)
{
	"use strict";

	jQuery.fn.mailcrypt = function( options )
	{
		return this.each(function()
		{
			var at = '@';

			if( jQuery('span', this).hasClass('mc-inner-hidden') ) {

				// insert visivle @ sign
				jQuery('.mc-inner-hidden *:first-child', this).replaceWith(at);

				// get complete mail address
				var email = jQuery('.mc-inner-hidden', this).text();
				jQuery('.mc-inner-hidden', this).remove();

			} else {
				// insert visivle @ sign
				jQuery(':first-child', this).replaceWith(at);

				// get complete mail address
				email = jQuery(this).text();
			}

			// place mailto link in the href
			jQuery(this).attr('href', 'mailto:'+email);
		});
	};
})( jQuery );

jQuery(document).ready(function(){
    "use strict";
	jQuery('a.mailcrypt').mailcrypt();
});
