'use strict';

angular.module('angularify.semantic.dropdown', ['ngSanitize'])
.controller('DropDownController', ['$scope',
    function($scope) {
        $scope.items = [];

        this.add_item = function(scope) {
            $scope.items.push(scope);

            scope.$on('$destroy', function(event) {
                this.remove_accordion(scope);
            });

            return $scope.items;
        };

        this.remove_item = function(scope) {
            var index = $scope.items.indexOf(scope);
            if (index !== -1)
                $scope.items.splice(index, 1);
        };

        this.update_title = function(title) {
            var i = 0;
            for (i in $scope.items) {
                $scope.items[i].title = title;
            }
        };        

    }
])
.directive('ngDropdownMenu_', function($timeout, $window) {        
    return {
        restrict: 'A',
        scope: {
            'items': '=items'
        },
        link: function(scope, element, attrs, ctrl) {
            element.bind('click', function(e) {
                //angular.element('.dropdown').not(element).children('.menu').removeClass('visible');
                element.toggleClass('visible active');
                scope.visible = !scope.visible;
                //e.stopPropagation();
            });         
            angular.element($window).on('click', function(e) {
                element.children('.menu').removeClass('visible');
            }); 
        }
    };
})
.directive('ngDropdownMenu', function($timeout, $window) {        
    return {
        restrict: 'A',
        link: function(scope, element, attrs, ctrl) {
            element.bind('click', function(e) {
                angular.element('.dropdown').not(element).children('.menu').removeClass('visible');
                element.toggleClass('visible active');
                element.children('.menu').toggleClass('visible');   
                e.stopPropagation();
            });         
            angular.element($window).on('click', function(e) {
                element.children('.menu').removeClass('visible');
            }); 
        }
    };
})
.directive('repeatItem', function($timeout, $window, $compile) {      
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            console.log('link');
        }
    };
})
.filter('objectFilter', function() {    
    return function(input, expected) {     
        var filteredInput = {};
        if( expected !== undefined ) {
            angular.forEach(input, function(value, key) {
                if( value.indexOf(expected) !== -1 || key.indexOf(expected) !== -1 ) {
                    filteredInput[key]= value;
                }
            });
            return filteredInput;
        }
        return input;
    };
})
.directive('ngDropdownSearchMenu', function($timeout, $window, $compile, $filter) {      
    return {
        restrict: 'A',
        transclude: true,
        replace: true,
        scope: {
            title: '@',
            ngModel: '=',
            ngChange: '&',
            items: '='
        },
        template: '<div ng-transclude></div>',
        link: {
            post: function(scope, element, attrs, ctrl, transclude) {

                transclude(scope, function(clone) {
                    var innerElement = angular.element(
                        '<input class="search" ng-model="search" />' +
                        '<div class="text" ng-class="{default: !ngModel, filtered: searching||search}" ng-bind-html="title">{{ title }}</div>' + 
                        '<div class="menu">' +
                            '<div class="item" ng-repeat="(id, value) in items | objectFilter: search" ng-click="select(id, value)"><i class="marker icon"></i>{{ value }}</div>' +
                        '</div>'
                    );   
                    element.append($compile(innerElement)(scope));
                });

                scope.$watch('ngModel', function() {                    
                    if (angular.isObject(scope.items) && scope.items.hasOwnProperty(scope.ngModel))
                        scope.title = scope.items[scope.ngModel];
                });
                
                scope.$watch('search', function() {
                });
                
                scope.searching = false;

                element.children('input.search').bind('keyup', function(e) {
                });
                
                scope.select = function(id, value) {
                    scope.ngModel = id;
                    $timeout(scope.ngChange, 0);
                };
                
                element.bind('click', function(e) {
                    angular.element('.dropdown').not(element).removeClass('active visible');       
                    angular.element('.dropdown').not(element).children('.menu').removeClass('transition visible');                

                    if( !element.children('input.search').is(':focus') || !element.hasClass('active visible') ) {
                        element.toggleClass('active visible');
                        element.children('.menu').toggleClass('transition visible');  
                    }                   
                    
                    element.children('input.search').focus();

                    e.stopPropagation();
                    //if( element.children('input.search'). )

                });         
                angular.element($window).on('click', function(e) {
                    element.removeClass('active visible');
                    element.children('.menu').removeClass('transition visible');
                }); 
            }
        }
    };
})
.directive('ngDropdown', function($timeout) {
    return {
        restrict: 'A',
        //replace: true,
        transclude: true,
        //require: '^ngModel',
        scope: {
            items: '=',
            ngModel: '=',
            ngChange: '&',
            title: '@title'
        },
        template: 
            '<i class="dropdown icon"></i>' +
            '<input class="search" tabindex="0">' +
            '<div class="text" ng-class="{default:!ngModel}">{{ name }}</div>' +
            '<div class="menu transition" ng-class="{visible: open}" tabindex="-1">' +
               '<div ng-repeat="item in items" class="item" ng-click="select(item)"><i class="icon" ng-class="{table: !item.compact, linkify: item.compact}"></i>{{ item.sheetName }}</div>' +                        
            '</div>',
        controller: function($scope) {
        },
        link: function(scope, element, attrs, ctrl, transclude) {
            scope.open = false;
            scope.name = scope.title;
            
            element.bind('click', function() {
                console.log(element.children('i')[0]);
                scope.$apply(function() {
                    scope.open = !scope.open;
                });                
            });
            scope.select = function(item) {
                scope.name = item.sheetName;
                scope.ngModel = item;
                $timeout(scope.ngChange, 0);
            };
            transclude(scope, function(clone, scope) {                
                element.append(clone);
            });
        }
    };
})
.directive('dropdown', function() {
    return {
        restrict: 'E',
        replace: true,
        transclude: true,
        controller: 'DropDownController',
        scope: {
            dropdown_class: '@dropdownClass',
            title: '@',
            open: '@',
            model: '=ngModel'
        },
        template: 
                '<div class="{{dropdown_class}}" ng-class="{visible:isDropdown, active:isDropdown}">{{title}}' +                     
                    '<i class="wrench icon"></i>{{title}}' + 
                    '<span class="text">{{title}}</span >' + 
                    '<div class="menu {{menu_class}}" ng-transclude>' + '</div>' + 
                '</div>',
        link: function(scope, element, attrs, DropDownController) {
            //scope.dropdown_class = 'ui selection dropdown';
            if (scope.open === 'true') {
                scope.isDropdown = scope.open = true;
                //scope.dropdown_class = scope.dropdown_class + ' active visible';
                scope.menu_class = 'transition visible';
            } else {
                scope.isDropdown = scope.open = false;                
            }
            DropDownController.add_item(scope);

            //
            // Watch for title changing
            //
            scope.$watch('title', function(val) {
                if (val === undefined)
                    return;

                if (val === scope.title)
                    return;

                scope.model = val;
            });

            //
            // Watch for ng-model changing
            //
            scope.$watch('model', function(val) {
                // update title
                scope.model = val;
                DropDownController.update_title(val);
            });

            //
            // Click handler
            //
            element.bind('click', function() {
                if (scope.isDropdown === false) {
                    scope.isDropdown = true;
                    scope.$apply(function() {
                        //scope.dropdown_class = 'ui selection dropdown active visible';
                        scope.menu_class = 'transition visible';
                    });
                } else {
                    scope.isDropdown = false;
                    scope.model = scope.title;
                    scope.$apply(function() {
                        //scope.dropdown_class = 'ui selection dropdown';
                        scope.menu_class = 'transition hidden';
                    });
                }
            });
        }
    };
})

.directive('dropdownGroup', function() {
    return {
        restrict: 'AE',
        replace: true,
        transclude: true,
        require: '^dropdown',
        scope: {
            title: '=title'
        },
        template: '<div class="item" ng-transclude >{{title}}</div>',
        link: function(scope, element, attrs, DropDownController) {

            // Check if title= was set... if not take the contents of the dropdown-group tag
            // title= is for dynamic variables from something like ng-repeat {{variable}}
            var title;
            if (scope.title === undefined) {
                title = scope.title;
            } else {
                title = element.children()[0].innerHTML;
            }

            //
            // Menu item click handler
            //
            element.bind('click', function() {
                
                DropDownController.update_title(scope.title);
            });
        }
    };
});
