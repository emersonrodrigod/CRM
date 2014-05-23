angular.module('ssi', [])
        .directive('gfTap', function() {
    return function(scope, element, attrs) {
        var tapping;

        tapping = false;
        element.bind('touchstart', function() {
            return tapping = true;
        });
        element.bind('touchmove', function() {
            return tapping = false;
        });
        return element.bind('touchend', function() {
            if (tapping) {
                return scope.$apply(attrs['gfTap']);
            }
        });
    };
});


