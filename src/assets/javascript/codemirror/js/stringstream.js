
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

/* String streams are the things fed to parsers (which can feed them
 * to a tokenizer if they want). They provide peek and next methods
 * for looking at the current character (next 'consumes' this
 * character, peek does not), and a get method for retrieving all the
 * text that was consumed since the last time get was called.
 *
 * An easy mistake to make is to let a StopIteration exception finish
 * the token stream while there are still characters pending in the
 * string stream (hitting the end of the buffer while parsing a
 * token). To make it easier to detect such errors, the stringstreams
 * throw an exception when this happens.
 */

// Make a stringstream stream out of an iterator that returns strings.
// This is applied to the result of traverseDOM (see codemirror.js),
// and the resulting stream is fed to the parser.
window.stringStream = function(source){
  // String that's currently being iterated over.
  var current = "";
  // Position in that string.
  var pos = 0;
  // Accumulator for strings that have been iterated over but not
  // get()-ed yet.
  var accum = "";
  // Make sure there are more characters ready, or throw
  // StopIteration.
  function ensureChars() {
    while (pos == current.length) {
      accum += current;
      current = ""; // In case source.next() throws
      pos = 0;
      try {current = source.next();}
      catch (e) {
        if (e != StopIteration) throw e;
        else return false;
      }
    }
    return true;
  }

  return {
    // Return the next character in the stream.
    peek: function() {
      if (!ensureChars()) return null;
      return current.charAt(pos);
    },
    // Get the next character, throw StopIteration if at end, check
    // for unused content.
    next: function() {
      if (!ensureChars()) {
        if (accum.length > 0)
          throw "End of stringstream reached without emptying buffer ('" + accum + "').";
        else
          throw StopIteration;
      }
      return current.charAt(pos++);
    },
    // Return the characters iterated over since the last call to
    // .get().
    get: function() {
      var temp = accum;
      accum = "";
      if (pos > 0){
        temp += current.slice(0, pos);
        current = current.slice(pos);
        pos = 0;
      }
      return temp;
    },
    // Push a string back into the stream.
    push: function(str) {
      current = current.slice(0, pos) + str + current.slice(pos);
    },
    lookAhead: function(str, consume, skipSpaces, caseInsensitive) {
      function cased(str) {return caseInsensitive ? str.toLowerCase() : str;}
      str = cased(str);
      var found = false;

      var _accum = accum, _pos = pos;
      if (skipSpaces) this.nextWhileMatches(/[\s\u00a0]/);

      while (true) {
        var end = pos + str.length, left = current.length - pos;
        if (end <= current.length) {
          found = str == cased(current.slice(pos, end));
          pos = end;
          break;
        }
        else if (str.slice(0, left) == cased(current.slice(pos))) {
          accum += current; current = "";
          try {current = source.next();}
          catch (e) {break;}
          pos = 0;
          str = str.slice(left);
        }
        else {
          break;
        }
      }

      if (!(found && consume)) {
        current = accum.slice(_accum.length) + current;
        pos = _pos;
        accum = _accum;
      }

      return found;
    },

    // Utils built on top of the above
    more: function() {
      return this.peek() !== null;
    },
    applies: function(test) {
      var next = this.peek();
      return (next !== null && test(next));
    },
    nextWhile: function(test) {
      var next;
      while ((next = this.peek()) !== null && test(next))
        this.next();
    },
    matches: function(re) {
      var next = this.peek();
      return (next !== null && re.test(next));
    },
    nextWhileMatches: function(re) {
      var next;
      while ((next = this.peek()) !== null && re.test(next))
        this.next();
    },
    equals: function(ch) {
      return ch === this.peek();
    },
    endOfLine: function() {
      var next = this.peek();
      return next == null || next == "\n";
    }
  };
};
