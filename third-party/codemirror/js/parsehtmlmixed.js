
/*
 Copyright (c) 2007-2010 Marijn Haverbeke

 This software is provided 'as-is', without any express or implied
 warranty. In no event will the authors be held liable for any
 damages arising from the use of this software.

 Permission is granted to anyone to use this software for any
 purpose, including commercial applications, and to alter it and
 redistribute it freely, subject to the following restrictions:

 1. The origin of this software must not be misrepresented; you must
    not claim that you wrote the original software. If you use this
    software in a product, an acknowledgment in the product
    documentation would be appreciated but is not required.

 2. Altered source versions must be plainly marked as such, and must
    not be misrepresented as being the original software.

 3. This notice may not be removed or altered from any source
    distribution.

 Marijn Haverbeke
 marijnh@gmail.com
*/

/**
 * CodeMirror
 *
 * Altered source version: Added License, Copyright and Info
 *
 * @author      Marijn Haverbeke marijnh@gmail.com
 * @copyright   (c) 2007-2010 Marijn Haverbeke
 * @license     http://codemirror.net/LICENSE zlib
 * @link        http://codemirror.net/
 * @category    Aiki
 * @package     CodeMirror
 * @filesource
 */

var HTMLMixedParser = Editor.Parser = (function() {
  if (!(CSSParser && JSParser && XMLParser))
    throw new Error("CSS, JS, and XML parsers must be loaded for HTML mixed mode to work.");
  XMLParser.configure({useHTMLKludges: true});

  function parseMixed(stream) {
    var htmlParser = XMLParser.make(stream), localParser = null, inTag = false;
    var iter = {next: top, copy: copy};

    function top() {
      var token = htmlParser.next();
      if (token.content == "<")
        inTag = true;
      else if (token.style == "xml-tagname" && inTag === true)
        inTag = token.content.toLowerCase();
      else if (token.content == ">") {
        if (inTag == "script")
          iter.next = local(JSParser, "</script");
        else if (inTag == "style")
          iter.next = local(CSSParser, "</style");
        inTag = false;
      }
      return token;
    }
    function local(parser, tag) {
      var baseIndent = htmlParser.indentation();
      localParser = parser.make(stream, baseIndent + indentUnit);
      return function() {
        if (stream.lookAhead(tag, false, false, true)) {
          localParser = null;
          iter.next = top;
          return top();
        }

        var token = localParser.next();
        var lt = token.value.lastIndexOf("<"), sz = Math.min(token.value.length - lt, tag.length);
        if (lt != -1 && token.value.slice(lt, lt + sz).toLowerCase() == tag.slice(0, sz) &&
            stream.lookAhead(tag.slice(sz), false, false, true)) {
          stream.push(token.value.slice(lt));
          token.value = token.value.slice(0, lt);
        }

        if (token.indentation) {
          var oldIndent = token.indentation;
          token.indentation = function(chars) {
            if (chars == "</")
              return baseIndent;
            else
              return oldIndent(chars);
          }
        }

        return token;
      };
    }

    function copy() {
      var _html = htmlParser.copy(), _local = localParser && localParser.copy(),
          _next = iter.next, _inTag = inTag;
      return function(_stream) {
        stream = _stream;
        htmlParser = _html(_stream);
        localParser = _local && _local(_stream);
        iter.next = _next;
        inTag = _inTag;
        return iter;
      };
    }
    return iter;
  }

  return {make: parseMixed, electricChars: "{}/:"};

})();
