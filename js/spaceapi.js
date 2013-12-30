$(document).ready(function($){

    var Application = {

        run: function() {
            this.view = $("#button, #circle3371, #path3339");
            this.title = $("h2");
            this.load_status();
        },

        load_status: function() {

            $.getJSON("/status/json", function(data){

                if(data !== null && typeof data === 'object'
                    && data.hasOwnProperty('state')
                    && data.state.hasOwnProperty('open'))
                {
                    switch(data.state.open)
                    {
                        case true:

                            Application.render('open');
                            break;

                        case false:

                            Application.render('closed');
                            break;

                        default:

                            Application.render('undefined');
                    }
                }
            });
            setTimeout(Application.load_status, 30000);
        },

        render: function(status) {

            this.title.text(status.toUpperCase());
            this.view.attr('class', status);
        }
    }

    Application.run();
});