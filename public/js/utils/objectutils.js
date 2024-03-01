var GetObjectMinKeyByIndex = (_obj) => {
    var min = 10000000000000000000;
    let minkey = "";

    for (var p = 0; p < Object.keys(_obj).length; p++) {
        var paramobj = _obj[Object.keys(_obj)[p]];

        if (paramobj.index <= min) {
            min = paramobj.index;
            minkey = Object.keys(_obj)[p];
        }
    }

    return minkey;
};

var GetObjectMaxKeyByIndex = (_obj) => {
    var max = -10000000000000000000;
    let maxkey = "";

    for (var p = 0; p < Object.keys(_obj).length; p++) {
        var paramobj = _obj[Object.keys(_obj)[p]];

        // REVIEW CHANGED Here
        if (paramobj.index >= max) {
            max = paramobj.index;
            maxkey = Object.keys(_obj)[p];
        }
    }

    return maxkey;
};

var GetArrayMinKeyByIndex = (_array) => {
    var lowest = _array[_array.length - 1];
    var tmp;
    for (var i = _array.length - 1; i >= 0; i--) {
        if (_array[i].index < lowest.index) lowest = _array[i];
    }
    return lowest;
};

var GetArrayMaxKeyByIndex = (_array) => {
    var highest = _array[_array.length - 1];
    var tmp;
    for (var i = _array.length - 1; i >= 0; i--) {
        if (_array[i].index > highest.index) highest = _array[i];
    }
    return highest;
};

var getRandomInt = (min, max) => {
    return Math.floor(Math.random() * (max - min + 1)) + min;
};

var SplitNumberIntoParts = (length, sum) => {
    var surplus = 40;
    var maxsurplus = surplus / 2;
    var initialcellsize = 100 / length;
    var maxcellsize = initialcellsize + maxsurplus;
    var probability = 0.6;

    var collection = [];
    var remaining = surplus;
    for (var i = 0; i < length; i++) {
        collection.push(initialcellsize);
    }

    if (length === 2) {
        var P = getRandomInt(0, 1);
        var R = getRandomInt(0, maxsurplus);        

        if (P >= probability) {
            var newNum = collection[0] + R;            
        } else {
            var newNum = collection[0] - R;
        }

        collection[0] = newNum;
        collection[1] = 100 - newNum;
    } else {
        var index = 0;
        var safecount = 100;
        while (remaining > 0 && safecount > 0) {
            var max = maxsurplus;
            if (max > remaining) max = remaining;
            var P = getRandomInt(0, 1);

            if (P >= probability) {
                var diff = maxcellsize - collection[index];
                if (diff > 0) {
                    var R = getRandomInt(0, maxsurplus);
                    if (R > diff) R = diff;
                    collection[index] += R;

                    remaining -= R;
                }
            }
            safecount--;
            index++;
            if (index > collection.length - 1) index = 0;
        }
    }

    return collection;
};

export { GetObjectMinKeyByIndex, GetObjectMaxKeyByIndex, GetArrayMaxKeyByIndex, GetArrayMinKeyByIndex, SplitNumberIntoParts };
