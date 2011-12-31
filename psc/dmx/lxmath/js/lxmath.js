var LXMATH = (function () {
  "use strict";

  var itself = {}, options, crunch;

  // Default options.  If you add controls for currently hidden options,
  // also add update controls to options.optupdate. If you change a default
  // without giving control tell the user on the page.
  // Unistart can be changed to 0 to show native artnet universe numbers.
  // Dipstart can be changed to 0 for European standard dipswitches.
  options = {
    split: 512,
    switches: true,
    addrstart: 1,
    unistart: 1,
    dipstart: 1,
    optupdate: function () {
      this.split = (document.forms.formin.numbertype.value === "dmx-address" ? 512 : 256);
      this.switches = document.forms.formin.dipswitch.checked;
    }
  };
  // Helper functions for mathify.
  crunch = {
    big: function (i) {
      if (options.split === 512) {
        return Math.floor((i - options.unistart) / options.split) + options.unistart;
      }
      else if (options.split === 256) {
        return Math.floor(i / options.split);
      }
      else {
        console.log("options.split out of range");
        return i;
      }
    },
    little: function (i) {
      if (options.split === 512) {
        return (i - options.addrstart) % options.split + options.addrstart;
      }
      else if (options.split === 256) {
        return (i % options.split);
      }
      else {
        console.log("options.split out of range");
        return i;
      }
    }
  };


  itself.mathify = function () {
    // Takes the input from formin, processes it and 
    // outputs it to the maths div using innerHTML
    options.optupdate();
    var i, individual, output = "", realaddress, binaryout, j;
    individual = document.forms.formin.dmxvalues.value.
      replace(/(\d+)\/(\d+)/g, function (match) {
        // change universe/address or coarse/fine to the absolute number
        match = match.split("/");
        var expando = ((parseInt(match[0], 10) - (options.split === 512 && options.unistart)) * options.split) + parseInt(match[1], 10);
        return expando;
      }).
      replace(/(\d+)-(\d+)/g, function (match) {
        // Chage ranges into comma seperated string list
        match = match.split("-");
        var expando = '';
        for (i = parseInt(match[0], 10); i < parseInt(match[1], 10); i += 1) {
          expando += i + ", ";
        }
        expando += match[1];
        return expando;
      }).
      split(",");
    // Start to generate the HTML for the results
    output += "<table class='center'>";
    output += "<th>Combined</th>";
    if (options.split === 512) {
      output += "<th>Universe</th>";
      output += "<th>Address</th>";
    }
    if (options.split === 256) {
      output += "<th>Coarse</th>";
      output += "<th>Fine</th>";
    }
    if (options.split === 512 && options.switches) {
      output += "<th>Visual DIP Switches</th>";
    }
    // Process the list of input numbers, and place them in
    // the output table.  crunch does the hard work.
    for (i = 0; i < individual.length; i += 1) {
      output += "<tr>";
      output += "<td>" + individual[i] + "</td>";
      output += "<td>" + crunch.big(individual[i]) + "</td>";
      realaddress = crunch.little(individual[i]);
      output += "<td>" + realaddress + "</td>";
      if (options.split === 512 && options.switches) {
        binaryout = realaddress.toString(2).slice(-9).split("").reverse().join("");
        for (j = 9 - binaryout.length; j > 0; j += -1) {
          binaryout = binaryout + "0";
        }
        output += "<td>" + binaryout + "</td>";
      }
      output += "</tr>";

    }
    output += "</table>";
    document.getElementById('maths').innerHTML = output;
  };

  itself.urlupdate = function () {
    // Use the history api so forward, back, and links work logically.
    var pushurl,
      valuein = document.forms.formin.dmxvalues.value,
      checkbox = '';
    if (document.formin.dipswitch.checked) {
      checkbox = "&dipswitch=" + document.formin.dipswitch.value;
    }
    pushurl = "index.html?dmxvalues=" +
      valuein.replace(/ /g, '+').replace(/,/g, "%2c") +
      "&numbertype=" + document.formin.numbertype.value + checkbox;
    history.pushState("", "", pushurl);
  };

  itself.procform = function (event) {
    event.preventDefault();
    LXMATH.mathify();
    LXMATH.urlupdate();
  };

  itself.keyup = function (event) {
    // Update on ','
    if (event.keyIdentifier === 'U+00BC') {
      LXMATH.mathify();
      LXMATH.urlupdate();
    }
  };

  itself.clicky = function () {
    LXMATH.mathify();
    LXMATH.urlupdate();
  };

  itself.lxmath = itself;
  itself.edition = '2011-12-2';
  // Everything to be available globally is assigned to itself, which
  // is returned and assigned to LXMATH
  return itself;

}());

(function () {
  "use strict";
  // Setting up event handlers.

  function loadFormFromURL (event) {
    // Function to parse info in the current url into the form.
    var oldurl = document.location.search.replace(/\+/g, " ").replace(/%2c/g, ","),
      dmxvalregex = /(?:dmxvalues=)((\d| |,|-|\/)*)/g,
      numtyperegex = /(?:numbertype=)(dmx-address|dmx-value)/g,
      showswitchregex = /(?:dipswitch=)(show-switch)/g,
      dmxval = dmxvalregex.exec(oldurl),
      numtype = numtyperegex.exec(oldurl),
      showswitch = showswitchregex.exec(oldurl);
    document.forms.formin.dmxvalues.value = dmxval && dmxval[1];
    document.formin.numbertype.value = numtype && numtype[1];
    document.formin.dipswitch.checked = showswitch && showswitch[1] ? true : false;
    LXMATH.mathify();
  };

  window.addEventListener('load', loadFormFromURL, false);
  window.addEventListener('popstate', loadFormFromURL, false);
  document.formin.addEventListener('submit', LXMATH.procform, false);
  document.formin.dmxvalues.addEventListener('keyup', LXMATH.keyup, false);
  document.formin.dipswitch.addEventListener('click', LXMATH.clicky, false);
  document.formin.numbertype.addEventListener('change', LXMATH.clicky, false);

}());
