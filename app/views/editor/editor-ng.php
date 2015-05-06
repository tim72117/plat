<head>
<!--<script src="/editor/js/jquery-ui-1.9.2.custom.min.js"></script>-->
<script src="/js/ckeditor/4.4.7-basic-source/ckeditor.js"></script>
<!--<script src="/js/textAngular/textAngular-rangy.min.js"></script>
<script src="/js/textAngular/textAngular-sanitize.min.js"></script>
<script src="/js/textAngular/textAngular.min.js"></script>-->
<!--<script src="/js/textAngular/ng-ckeditor.js"></script>-->

<link rel="stylesheet" href="/editor/css/editor/structure_new.css" />
<link rel='stylesheet prefetch' href='http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'>
</head>


<?
//
//User::
//
//$result = array_merge($a, $b);
//print_r($result);
?>
	
<div ng-controller="editorController" ng-click="resetEditor()">
<div style="margin:0 auto;width:300px;text-align: left;float:left">
	
	<div style="position:fixed;width:100px;background-color:#fff;border-top:0;z-index:1">
		<div style="background-color:#fff;font-size:14px">	
            
            <div class="file-btn" style="width:70px;height:25px;line-height:25px;background-color: #eee;float:left;margin:0 0 0 2px" ng-click="" disabled="disabled">刪除整頁</div>
            <div class="file-btn" style="width:70px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px" ng-click="get_from_xml()">讀取xml</div>
            <div class="file-btn" style="width:70px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px" ng-click="get_from_db()">讀取db</div>
            <div class="file-btn" style="width:70px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px" ng-click="save_to_db()" ng-disabled="false&&!edit">儲存db</div>
            <div class="file-btn" style="width:30px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px" ng-click="prev_part()"><</div>
            {{ page }}
            <div class="file-btn" style="width:30px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px" ng-click="next_part()">></div>
            <div class="file-btn" style="width:40px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px"><a href="demo?page=1" target="_blank">預覽</a></div>
            <div class="file-btn" style="width:60px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px"><a href="demo_ng" target="_blank">預覽ng</a></div>
            <div class="file-btn" style="width:70px;height:25px;line-height:25px;background-color: #eee;float:left;margin:2px 0 0 2px"><a href="/editor/creatTable">建立問卷</a></div>
            <div style="float:left;font-size:16px" ng-if="update_mis>1">儲存中{{ update_mis }}...</div>
            <div style="width:150px;float:left;font-size:14px;color:#aaa" ng-if="update_mis===1">所有變更都已經儲存</div>
		</div>
        
    </div>
</div>
	
<div id="building" style="border:0px dashed #A0A0A4;border-top:0;border-bottom:0;z-index:1;padding-left:200px;padding-right:200px">

	<div id="contents" style="margin-left:0px">
        
        <div class="main">
            <div class="addq_box" align="left" style="padding: 2px;background-color:#fff;margin-left:0">
                <span class="cut-page" title="加入分頁" ng-click="ques_add_ques(questions, $index+1, question.layer)" style="display: inline-block"></span>
            </div>
        </div>
        <div ng-repeat="page in pages | filter:{selected: true}" style="border: 1px solid black;padding:10px">
            
            <div class="main">
                <div class="addq_box" align="left" style="padding: 2px;background-color:#fff;margin-left:0">
                    <span class="addquestion" title="加入題目" ng-click="ques_add_ques(questions, $index+1, question.layer)" style="display: inline-block"></span>
                </div>
            </div>
            <questions data="page.data" layer="0" update="update"></questions>  
        </div>
        
	</div>

	<div id="footer" style="margin-bottom:20px"></div>
    
</div>
</div><!-- controller  -->

<script>
angular.module('app', [])
.filter('startFrom', function() {
    return function(input, start) {
        if( typeof(input) !== 'undefined' )
            return input.slice(start);
    };
})
.controller('editorController', editorController)
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
})
.directive('contenteditable', function(){
    return { 
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attributes, ngModel){
            ngModel.$render = function() {
                element.html(ngModel.$viewValue||'');
            };
            element.bind('blur keyup change', function() {
                //scope.$apply(function() {
                //    ngModel.$setViewValue(element.html());
                //});
            });
        }
    };        
})
.directive('questions', function(){
    return {
        restrict: 'E',
        replace: true,
        transclude: false,
        //require: '?ngAutoHeight',
        scope: {questions: '=data', layer: '=layer', parts: '=', update: '='},
        template: '<div ng-include src="\'template\'"></div>',
        link: function(scope, element, attrs) {
            //console.log();
        },
        controller: function($scope, $http, $interval) {
            
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
            
            $scope.ques_add_ques = function(questions, index, layer) {
                console.log(questions);
                questions.splice(index, 0 ,{
                    type: '?',
                    code: 'auto',
                    answers: [{subs:[]}]
                });                
                //$scope.updateStruct(questions);
            };
            
            $scope.ques_remove = function(questions, index) {
                questions.splice(index, 1);
                //$scope.updateStruct(questions);
            };
            
            $scope.ques_add_var = function(vars, index, obj) {
                obj = obj || {subs:[]};
                vars.splice(index, 0, obj);
            };  

            $scope.ques_remove_var = function(vars, index) {
                vars.splice(index, 1);
            }; 
            
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
});
function editorController($http, $scope, $sce, $interval, $filter) {
    
    $scope.pages = [];
    $scope.page = 0;
    $scope.update_mis = 0;
    $scope.editorOptions = {
        language: 'ru',
        uiColor: '#000000'
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
        }).error(function(e){
            console.log(e);
        });
    };    

    $scope.save_to_db = function() {
        $http({method: 'POST', url: 'save_ques_to_db', data:{pages: btoa(encodeURIComponent(angular.toJson($scope.pages)))} })
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.pages = data.struct;            
        }).error(function(e){
            console.log(e);
        });
    };   
    
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
    
    $scope.next_part = function() {
        if( $scope.page < $scope.pages.length ) {
            $scope.pages[$scope.page].selected = false;
            $scope.pages[++$scope.page].selected = true;
        }
    };
    
    $scope.prev_part = function() {
        if( $scope.page > 0 ) {
            $scope.pages[$scope.page].selected = false;
            $scope.pages[--$scope.page].selected = true;
        }
    };
    
}
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
.file-btn {
    cursor: pointer; 
    text-align: center;
    border: 1px solid #aaa;
    color:#555;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.file-btn:hover {
    border: 1px solid #888;
    color:#000;
}
</style>