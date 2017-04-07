var app = app || {};

app.request = (function(){

    var request = {
        sender: function(data){
            return $.ajax({
                url: data.AR_origin,
                type: "POST",
                data: data
            });
        },
        reconstruct: function(landMarkData, formData){
            //use object create to create new object w/o reference
            var data = Object.create(landMarkData), prop;

            for(prop in formData){
                data[formData[prop].name] = formData[prop].value;   
            }
            return data;
        }
    }
    return request;
}());