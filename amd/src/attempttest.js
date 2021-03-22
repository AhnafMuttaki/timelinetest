function clicksubmit() {
    // Alert("submit clicked");
    try {
        var selectedresponse = "";
        var phaseresponses = document.getElementsByName("phaseresponse");
        var responsecount = phaseresponses.length;

        if (responsecount > 1) {
            for (var i = 0; i < responsecount; i++) {
                if (phaseresponses[i].checked) {
                    var selectedresponse = phaseresponses[i].value;
                }
            }
        } else {
            var selectedresponse = document.getElementsByName("phaseresponse")[0].value;

        }

        if (selectedresponse == "") {
            console.log("faka paisi");
            var validationmsg = document.getElementById("chooseoptionvalidation").value;
            alert(validationmsg);
            return false;
        }
        console.log(selectedresponse);
    } catch (e) {
        console.log(e.toString());
    }
    return true;
}