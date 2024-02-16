const CalculateSize = (size) => {
    // size is in bytes
    var sizekb = parseInt(size / 1000);
    var size_str = sizekb + " KB";

    if (sizekb > 1000) {
        size_str = parseInt(sizekb / 1000) + " MB";
    }

    return size_str;
}

const GetDateFromTimestamp = (timestamp) => {
    const date = new Date(timestamp * 1000);
    let dateFormat = String(date.getDate()).padStart(2, '0') +
        "/" + String((date.getMonth() + 1)).padStart(2, '0') +
        "/" + date.getFullYear() +
        " " + String(date.getHours()).padStart(2, '0') +
        ":" + String(date.getMinutes()).padStart(2, '0') +
        ":" + String(date.getSeconds()).padStart(2, '0');

    return dateFormat;
}

const RemoveItemFromArraywithID = (arr, _id) => {
    var i = 0;
    while (i < arr.length) {
        if (arr[i].id === _id) {
            arr.splice(i, 1);
        } else {
            ++i;
        }
    }
    return arr;
}

export default { CalculateSize, GetDateFromTimestamp, RemoveItemFromArraywithID };