$(document).ready(function($){

    var Application = {

        run: function() {
            this.view = $("#status");
            this.load_status();
        },

        load_status: function() {

            $.getJSON("status.json", function(data){

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
            setTimeout(Application.load_status, 1000);
        },

        render: function(status) {
            this.view.removeClass('open');
            this.view.removeClass('closed');
            this.view.removeClass('undefined');
            this.view.addClass(status);
        }
    }

    Application.run();
});