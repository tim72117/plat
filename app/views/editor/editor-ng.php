<head>
<!--<script src="/editor/js/jquery-ui-1.9.2.custom.min.js"></script>-->
<script src="/js/ckeditor/4.4.7-basic-source/ckeditor.js"></script>
<!--<script src="/js/textAngular/ng-ckeditor.js"></script>-->

<link rel="stylesheet" href="/css/ui/Semantic-UI-1.11.4/semantic.min.css" />
</head>
	
<div ng-controller="editorController" ng-click="resetEditor()">
    
    <div class="ui left floated basic segment">
        <div class="ui vertical mini basic buttons">
            <div class="ui button disabled"><i class="icon trash"></i>刪除整頁</div>
            <div class="ui button" ng-click="get_from_xml()"><i class="icon trash"></i>讀取xml</div>
            <div class="ui button" ng-click="get_from_db()"><i class="icon trash"></i>讀取db</div>
            <div class="ui button" ng-click="save_to_db()" ng-disabled="false&&!edit"><i class="icon trash"></i>儲存db</div>
            <div class="ui button" ng-click="prev_page()"><i class="icon trash"></i>前一頁</div>
            <div class="ui button" ng-click="next_page()"><i class="icon trash"></i>下一頁</div>  
            <div class="ui button" ng-click="get_from_db_new()"><i class="icon trash"></i>讀取DB(新)</div>  
        </div>
        <br />
        <div class="ui vertical text menu">
            <a class="item" href="demo?page=1" target="_blank">預覽</a>
            <a class="item" href="demo_ng" target="_blank">預覽ng</a>
            <a class="item" href="/editor/creatTable">建立問卷</a>
        </div>
		
		<div class="ui mini basic button" ng-click="addQues(questions, $index+1, question.layer)"><i class="icon file outline"></i>加入分頁</div>
		<div class="ui mini basic button" ng-click="adding=true"><i class="icon help circle"></i>加入題目</div>
    </div>
    
    <div style="float:left;font-size:16px" ng-if="update_mis>1">儲存中{{ update_mis }}...</div>
    <div style="width:150px;float:left;font-size:14px;color:#aaa" ng-if="update_mis===1">所有變更都已經儲存</div>
	
    <div ng-cloak class="ui left floated basic segment" style="min-height: 600px;min-width: 800px">
			
		<div class="ui segment" ng-repeat="question in page.questions" ng-if="question.type != 'list'" question="question" layer="0" update="update"
            ng-mouseover="question.hover = true" ng-mouseleave="question.hover = false"></div>

        <div class="ui segment" ng-repeat="question in page.questions" ng-if="question.type == 'list'">

            <div class="ui form">
                <div class="field">
                    <textarea ng-model="question.title" placeholder="輸入題目標題..." style="resize: none"></textarea>
                </div>
            </div>
            
            <div class="ui accordion field">

                <div class="title" ng-class="{active: question.open.subs}" ng-click="question.open.subs = !question.open.subs">
                    <i class="dropdown icon"></i>題目
                </div>

                <div class="content" ng-if="question.subs.length > 0" ng-class="{active: question.open.subs}">
                    <div class="ui tertiary segment" ng-repeat="sub in question.subs">
                        <div class="ui vertical segment" question="sub" layer="0" update="update"></div>
                    </div>
                </div>

            </div>  

        </div>

    </div>
    
</div>

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {
        if( typeof(input) !== 'undefined' )
            return input.slice(start);
    };
})
.controller('editorController', function editorController($http, $scope, $sce, $interval, $filter) {
		
    $scope.pages = [];
    $scope.page = {};
    $scope.update_mis = 0;
    $scope.editorOptions = {
        language: 'ru',
        uiColor: '#000000'
    };
	
	$scope.adding = false;
	$scope.added = function() {
		$scope.adding = false;
	};
    
    $scope.resetEditor = function() {
        for(var i in CKEDITOR.instances) {
            console.log(CKEDITOR.instances[i]);
            CKEDITOR.instances[i].destroy(false);
        }        
    };
    
    $scope.get_from_xml = function() {
        $http({method: 'POST', url: 'get_ques_from_xml', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data.data;
            $scope.edit = data.edit;
            $scope.page = 0;
            $scope.pages[$scope.page].selected = true;
            //$scope.pages = data.pages;
            //$scope.page = $scope.pages[page-1];            
        }).error(function(e){
            console.log(e);
        });
    };
    
    $scope.update = function(update_mis) {
        $scope.update_mis = update_mis;
    };
    
    $scope.renderHtml = function(htmlCode) {
        return $sce.trustAsHtml(htmlCode);
    };
    
    $scope.ques_import_var = function(question) {        
        question.answers.length = 0;
        var list = question.importText.split('\n');
        for(index in list){	
            var itemn = list[index].split('	');
            question.answers.push({value:itemn[0], title:itemn[1]});
        }       
        question.code = 'manual';
        question.importText = null;
        question.is_import = false;
    };
    
    $scope.ques_add_page = function() {
        $http({method: 'POST', url: 'add_page', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
        }).error(function(e) {
            console.log(e);
        });
    };    

    $scope.save_to_db = function() {
        $http({method: 'POST', url: 'save_ques_to_db', data:{pages: btoa(encodeURIComponent(angular.toJson($scope.pages)))} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data.struct;            
        }).error(function(e) {
            console.log(e);
        });
    };  
    
    $scope.get_from_db_new = function() {
        $http({method: 'POST', url: 'get_ques_from_db_new', data:{} })
        .success(function(data, status, headers, config) {
            $scope.pages = data;
            $scope.page = $scope.pages[0];
            console.log($scope.pages);
        }).error(function(e) {
            console.log(e);
        });
    };
	
	$scope.get_from_db_new();
    
    $scope.get_from_db = function() {
        $http({method: 'POST', url: 'get_ques_from_db', data:{} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data;
            $scope.edit = true;
            $scope.page = 0;
            $scope.pages[$scope.page].selected = true;
//            $scope.$watch(function(){ return $scope.questions[0]; }, function(newValue, oldValue){
//                $scope.update_mis = 50;
//            }, true);
                    
        }).error(function(e){
            console.log(e);
        });
    };  
    
    $scope.save_ques_to_db = function() {
        
    };

    $scope.next_page = function() {
        var index = $scope.pages.indexOf($scope.page);
        if( index < $scope.pages.length )
            $scope.page = $scope.pages[++index];
    };
    
    $scope.prev_page = function() {
        var index = $scope.pages.indexOf($scope.page);
        if( index > 0 )
            $scope.page = $scope.pages[--index];
    };
    
})

.directive('contenteditable', function(){
    return { 
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attributes, ngModel){
            ngModel.$render = function() {
                element.html(ngModel.$viewValue || '');
            };
            element.bind('blur keyup change', function() {
                //scope.$apply(function() {
                //    ngModel.$setViewValue(element.html());
                //});
            });
        }
    };        
})
.directive('question', function($compile){
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        //require: '?ngAutoHeight',
        scope: {question: '=question', layer: '=layer', parts: '=', update: '=', index: '='},
		templateUrl: 'template',
        ///template: '<div ng-include src="\'template\'"></div>',
        compile: function(tElement, tAttr) {            
            var contents = tElement.contents().remove();
            var compiledContents;

            return function(scope, iElement, iAttr) {
                if( !compiledContents )
                    compiledContents = $compile(contents);

                scope.$watch('question.removed', function(removed) {
                    if( removed )
                        iElement.remove();
                });

                compiledContents(scope, function(clone, scope) {
                    iElement.append(clone); 
                });
            };      
        },
        link: function(scope, element, attrs) {			
            return function(scope, iElement, iAttr) {
                console.log(scope);
            };
        },
        controller: function($scope, $http, $interval, $timeout) {
            
            $scope.test = function(type) {
                console.log(type);
            };
            
            $scope.quesTypes = [
                {type: 'select', name: '單選題(下拉式)'},
                {type: 'radio', name: '單選題(點選)'},
                {type: 'checkbox', name: '複選題'},
                {type: 'text', name: '文字欄位'},
                {type: 'textarea', name: '文字欄位(大型欄位)'},
                {type: 'textscale', name: '文字欄位(表格)'},
                {type: 'scale', name: '量表'},
                {type: 'list', name: '題組'},
                {type: 'table', name: '表格'},
                {type: 'explain', name: '文字標題'}
            ]; 
            
            $scope.addQues = function(question, index, answer) {
                answer = answer || {};
                question.subs = question.subs || [];
                question.subs.splice(index, 0, {
                    title: '',    
                    type: '?',
                    code: 'auto',
                    answers: [],
                    subs: [],
                    parent_value: answer.value || null 
                });                
                //$scope.updateStruct(questions);
            };
            
            $scope.removeQues = function(question, index) {
                question.removed = true;
                //$scope.updateStruct(questions);
            };

            $scope.addSub = function(subs, index, obj) {
                console.log(index);
                obj = obj || {};
                subs.splice(index, 0, obj);
            };
            
            $scope.addAns = function(answers, index, obj) {
                obj = obj || {};
                answers.splice(index, 0, obj);
                $scope.resetAnswers(answers);
            };  

            $scope.removeAns = function(answers, index) {
                answers.splice(index, 1);
                $scope.resetAnswers(answers);
            }; 

            $scope.resetAnswers = function(answers) {
                for(index in answers) {
                    answers[index].value = index*1+1;
                }
            };

            $scope.hideOptions = function(question, index) {
                $timeout(function() {
                    question.open.button[index] = false;
                }, 300);
            }
            
            $scope.typeChange = function(question) {
                if( question.type==='textarea'  ){
                    {struct:{}}
                }
                if( question.type==='table'  ){
                    if( typeof(question.degrees)==='undefined' )
                        question.degrees = [];
                }
            };
            
            $scope.update_mis = 5;
            
            $scope.update_count = function(n, update) {
                var mis = n;
                if( angular.isDefined($scope.stopFight) ) {
                    $interval.cancel($scope.stopFight);
                    $scope.stopFight = undefined;
                }
                $scope.stopFight = $interval(function() {                    
                    if( mis > 1 ) {
                        $scope.update(mis); 
                        mis--;
                    }else{
                        $scope.update(mis);
                        //update();
                    }                   
                }, 1000, n);
            };
            
            $scope.updateQueue = {};
            $scope.updateQuestion = function(question) {
                console.log(question);return false;
                $scope.updateQueue[question.id] = question;
                $scope.update_count(5 ,function(){
                    var myArr = Object.keys($scope.updateQueue).map(function (key) {return $scope.updateQueue[key];});
                    $http({method: 'POST', url: 'update_ques_to_db', data:{updateQueue: btoa(encodeURIComponent(angular.toJson($scope.updateQueue)))} })
                    .success(function(data, status, headers, config) {
                        console.log(data);
                        //$scope.questions = data;
                    }).error(function(e){
                        console.log(e);
                    });
                    $scope.updateQueue = {};
                });
            };
            
            $scope.updateStruct = function(questions) {
                $http({method: 'POST', url: 'save_ques_struct_to_db', data:{questions: questions} })
                .success(function(data, status, headers, config) {
                    console.log(data);
                    $scope.questions = data;
                }).error(function(e){
                    console.log(e);
                });
            };
    
        }
    };    
})
.directive('ngAutoHeight', function(){
    return { 
        restrict: 'A',
        replace: true,
        link: function(scope, element, attributes){
            setTimeout( function() {
                element[0].style.height = element[0].scrollHeight+2+'px';     
            }, 0);
            element.bind('keyup', function(){
                element[0].style.height = element[0].scrollHeight+2+'px';   
            });
        }
    };
})
.directive('ngEditor', function($timeout){
    return { 
        restrict: 'A',
        replace: true,
        require: '?ngModel',
//        compile: function(element) {
//            var edit_btn = element.append('<span class="style_edit"></div>');
//        },
        link: function(scope, element, attributes, ngModel){
            
            //element.parent().bind('click', function (e) {
                //e.stopPropagation();
            //});
            //console.log(edit_btn);
            element.on('click', function(e) {   
                var edit_btn = element.prepend('<span class="style_edit">1</div>');
                var config = {};
                config.toolbar =
                    [   
                        { name: 'document',    items : [ 'mode','Source','-','NewPage' ] },
                        //{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
                        { name: 'colors',      items : [ 'TextColor','BGColor' ] },
                        { name: 'styles',      items : [ 'FontSize' ] },
                        { name: 'basicstyles', items : [ 'Italic','Bold','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                        //{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
                        { name: 'links',       items : [ 'Link','Unlink' ] },
                        { name: 'insert',      items : [ 'Image','SpecialChar' ] }          
                    ];
                config.height = element[0].scrollHeight+40;
                config.readOnly = false;
                config.enterMode = CKEDITOR.ENTER_BR;
                config.startupFocus = true;

                var instance = CKEDITOR.replace(element[0], config);


                
                element.bind('$destroy', function () {
                    console.log(555);
                });
                
                var setModelData = function() {
                    var data = instance.getData();                    
                    $timeout(function () {                        
                        //ngModel.$setViewValue(data);
                    }, 0);
                };
                
                instance.on('instanceReady', function() {
                    instance.document.on('keyup', setModelData);
                    instance.on('destroy', function(e){
                        element.triggerHandler('blur');
                    });
                });
                
            });
            
//            ngModel.$render = function() {
//                element.html(ngModel.$viewValue || '');
//            };
        }
    };
});
</script>
<style>
div.title-editor {
    border: 1px solid #aaa;
    min-height: 30px;
    line-height: 30px;
    padding: 0 0 0 2px;
    font-size: 13px;
}
textarea.title-editor {
    resize: none;
    vertical-align: middle;
    box-sizing: border-box;
    padding: 6px 0 0 2px;
    height: 30px;
    width: 350px;    
}
input.editor {
    box-sizing: border-box;
    height: 30px;
    padding: 0 0 0 2px;
}
</style>