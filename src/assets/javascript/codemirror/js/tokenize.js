
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

// A framework for simple tokenizers. Takes care of newlines and
// white-space, and of getting the text from the source stream into
// the token object. A state is a function of two arguments -- a
// string stream and a setState function. The second can be used to
// change the tokenizer's state, and can be ignored for stateless
// tokenizers. This function should advance the stream over a token
// and return a string or object containing information about the next
// token, or null to pass and have the (new) state be called to finish
// the token. When a string is given, it is wrapped in a {style, type}
// object. In the resulting object, the characters consumed are stored
// under the content property. Any whitespace following them is also
// automatically consumed, and added to the value property. (Thus,
// content is the actual meaningful part of the token, while value
// contains all the text it spans.)

function tokenizer(source, state) {
  // Newlines are always a separate token.
  function isWhiteSpace(ch) {
    // The messy regexp is because IE's regexp matcher is of the
    // opinion that non-breaking spaces are no whitespace.
    return ch != "\n" && /^[\s\u00a0]*$/.test(ch);
  }

  var tokenizer = {
    state: state,

    take: function(type) {
      if (typeof(type) == "string")
        type = {style: type, type: type};

      type.content = (type.content || "") + source.get();
      if (!/\n$/.test(type.content))
        source.nextWhile(isWhiteSpace);
      type.value = type.content + source.get();
      return type;
    },

    next: function () {
      if (!source.more()) throw StopIteration;

      var type;
      if (source.equals("\n")) {
        source.next();
        return this.take("whitespace");
      }
      
      if (source.applies(isWhiteSpace))
        type = "whitespace";
      else
        while (!type)
          type = this.state(source, function(s) {tokenizer.state = s;});

      return this.take(type);
    }
  };
  return tokenizer;
}
