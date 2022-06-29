/* фронтэнд */
FE = {

    data:[],

    addAjaxData:function(data){

        var parsed_data = JSON.parse(data);
        for (var i in parsed_data) {

            this.data[i] = parsed_data[i];
        }
    },

    getData:function(index){

        if (typeof this.data[index] != 'undefined') {

            return this.data[index];

        } else {

            return NaN;
        }
    }

};