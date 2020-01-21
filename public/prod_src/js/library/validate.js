module.exports = {

    email:function(email) {

        var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
        var is_valid = re.test(email);
        return is_valid;
    },

    phone:function(phone) {
        var re = /^\d[\d\(\)\ -]{4,14}\d$/;
        var is_valid = re.test(phone);
        return is_valid;
    }

}

