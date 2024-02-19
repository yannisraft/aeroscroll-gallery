window["pro_func"] = {
    CheckLicense: (callback, NONCE) => {
        console.log("Checking License...");

        let _REST_URL = "http://localhost/";
        if (window["REST_URL"]) {
            _REST_URL = window["REST_URL"].url;
        }

        let finalurl = `${_REST_URL}/wp-json/aeroscroll/v1/manageserial?request=get`;
        fetch(finalurl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": NONCE
            }
        })
            .then((response) => {
                //console.log("GET LICENSE response: ", response.text());
                return response.json();
            })
            .then((result) => {
                callback(result);
            });
    }
};