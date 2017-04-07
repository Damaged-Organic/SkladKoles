var app = app || {};

app.historyFilter = (function() {

    var PARAMETER_DELIMITER = ';';
    var NESTED_DELIMITER = ':';

    var historyFilter = {
        getFilterParameters: function(formData, filter) {
            var filterParameters = [];

            formData.forEach(function(item) {
                var parsedName = item.name.match(/\[([\w\-]+?)\]/g).map(function(item) {
                    return item.replace(/^\[|\]$/g, "");
                });

                parsedName.forEach(function(parameter) {
                    filter(filterParameters, parameter, item, parsedName);
                });
            });

            return filterParameters;
        },

        getModificationFilter: function(filterParameters, parameter, item, parsedName) {
            if( parsedName.length === 2 ) {
                filterParameters[parsedName[1]] = item.value;
            } else {
                filterParameters[parsedName[0]] = item.value;
            }
        },

        getAutoFilter: function(filterParameters, parameter, item) {
            var name = parameter.replace(/\-/g, '_'),
                value = item.value.replace(/\s/g, '_');

            filterParameters[name] = value;
        },

        composeQueryString: function(filterParameters) {
            var queryArray = [];

			Object.keys(filterParameters).forEach(function(key) {
                var parameter = filterParameters[key];

				if( parameter )
					queryArray.push(key + "-" + parameter);
			});

			return queryArray.join(';');
        },

        replaceState: function(queryString) {
            var splitPathname = location.pathname.split('/');
            var joinedPathname = [
                splitPathname[0], splitPathname[1], splitPathname[2]
            ].join('/');

            window.history.replaceState({}, "", joinedPathname + "/" + queryString);
        }
    };

    return historyFilter;
}());
