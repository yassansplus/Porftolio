$(function () {
    setTimeout(function () {
        new TypeIt('#simpleUsage', {

            speed: 50,
            waitUntilVisible: true
        }).type('Bienvenu sur mon Portfolio')
            .pause(500)
            .delete()
            .type("Yass'Portfolio")
            .go();

    }, 3000)

})