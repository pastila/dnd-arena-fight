/* 
 * @author Denis N. Ragozin <ragozin at artsofte.ru>
 * @version SVN: $Id$
 * @revision SVN: $Revision$
 */
define(function(require){
  
  var Accounting = require('lib/accounting');
  
  Accounting.settings = {
    currency: {
      symbol : '₽',   // default currency symbol is 'RUB'
      format: "%v&nbsp;%s", // controls output: %s = symbol, %v = value/number (can be object: see below)
      decimal : ",",  // decimal point separator
      thousand: "&nbsp;",  // thousands separator
      precision : 0   // decimal places
    },
    number: {
      precision : 2,  // default precision on numbers is 0
      thousand: "&nbsp;",
      decimal : ","
    }
  };
  
  Number.prototype.toCurrencyString || (Number.prototype.toCurrencyString = function(symbol, precision, thousand, decimal){
    var val = (this instanceof Number) ?  this.valueOf() : this,
        ret = Accounting.formatMoney.apply(this, [val].concat(Array.prototype.slice.call(arguments, 0)));

    return ret;
  });
 
 String.formatEnding = function (v, wf){
      var md = Number(v) % 10;
      if (md === 1 && v % 100 !== 11){
        return wf[1];
      };
      var md100 = v % 100;
      if (md > 1 && md < 5 && (md100 < 10 || md100 > 20))
        return wf[2];
      return wf[0];
 };
  String.prototype.lpad = function(padString, length) {
    var str = this;
    while (str.length < length) {
      str = padString + str;
    }
    return str;
  }
});

