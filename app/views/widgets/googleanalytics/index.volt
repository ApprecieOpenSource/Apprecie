<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '{{config.analytics.trackingId}}', 'auto');
    ga('send', 'pageview');

    C = {
        // Number of days before the cookie expires, and the banner reappears
        cookieDuration : 14,

        // Name of our cookie
        cookieName: 'complianceCookie',

        // Value of cookie
        cookieValue: 'on',

        // Message banner title
        bannerTitle: "Cookies:",

        // Message banner message
        bannerMessage: "This website uses cookies for key functionality and analytics to provide the very best experience. You can continue to use the site as normal if you're happy with this, or you can <a href='/legal/terms' target='_blank'> click here</a> to find out more about how we use cookies and suggested action if you do not wish to use cookies.",

        // Message banner dismiss button
        bannerButton: "OK",

        createDiv: function () {
            var banner = $(
                '<div class="alert alert-success alert-dismissible fade in" ' +
                'role="alert" style="position: fixed; z-index:9999999; bottom: 0; width: 100%; ' +
                'margin-bottom: 0"><strong>' + this.bannerTitle + '</strong> ' +
                this.bannerMessage +'<button type="button" class="btn ' +
                'btn-success pull-right" onclick="C.createCookie(C.cookieName, C.cookieValue' +
                ', C.cookieDuration)" data-dismiss="alert" aria-label="Close">' +
                this.bannerButton + '</button></div>'
            )
            $("body").append(banner)
        },

        createCookie: function(name, value, days) {
            var expires = ""
            if (days) {
                var date = new Date()
                date.setTime(date.getTime() + (days*24*60*60*1000))
                expires = "; expires=" + date.toGMTString()
            }
            document.cookie = name + "=" + value + expires + "; path=/; secure";
        },

        checkCookie: function(name) {
            var nameEQ = name + "="
            var ca = document.cookie.split(';')
            for(var i = 0; i < ca.length; i++) {
                var c = ca[i]
                while (c.charAt(0)==' ')
                    c = c.substring(1, c.length)
                if (c.indexOf(nameEQ) == 0)
                    return c.substring(nameEQ.length, c.length)
            }
            return null
        },

        init: function() {
            if (this.checkCookie(this.cookieName) != this.cookieValue)
                this.createDiv()
        }
    }

    $(document).ready(function() {
        C.init()
    })
</script>