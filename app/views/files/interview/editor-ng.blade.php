
<div ng-cloak class="ui container basic segment" ng-class="{loading: loading}" ng-controller="editorController">

    <div class="ui basic segment">
        <div class="ui right labeled input">
            <input type="number" ng-model="page" ng-model-options="{updateOn: 'default blur', debounce:{default: 2000, blur: 0}}" placeholder="輸入頁數" ng-change="jumpPage()">
            <div class="ui basic label">總共 @{{ lastPage }} 頁 </div>
        </div>
        <div class="ui basic buttons" >
            <div class="ui button disabled">刪除整頁</div>
            <div class="ui button" ng-click="prevPage()">前一頁</div>
            <div class="ui button" ng-click="nextPage()">下一頁</div>
            <a class="ui button" href="demo" target="_blank">預覽</a>
            <a class="ui button" href="skip" target="_blank">跳題設定</a>
        </div>
    </div>

    <div class="ui basic segment">
        <div class="ui styled fluid accordion">
            <div class="title" ng-class="{active: opening.sbooks}" ng-click="opening.sbooks=!opening.sbooks"><i class="dropdown icon"></i> 設定使用題本</div>
            <div class="content" ng-class="{active: opening.sbooks}">
                <div class="list" style="max-height:500px;overflow-y:scroll">
                    <div class="item" ng-repeat="sbook in sbooks">
                        <div class="ui checkbox">
                            <input id="@{{::$id}}" type="checkbox" class="hidden" ng-model="sbook.checked" />
                            <label for="@{{::$id}}">@{{sbook.title}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div style="float:left;font-size:16px" ng-if="update_mis>1">儲存中@{{ update_mis }}...</div>
    <div style="width:150px;float:left;font-size:14px;color:#aaa" ng-if="update_mis===1">所有變更都已經儲存</div>

    <div class="ui basic segment">

        <div class="ui right aligned basic segment">
            <a class="ui mini top right attached label" ng-click="addQuestion(0)"><i class="add icon"></i>加入題目</a>
        </div>

        <div class="ui top attached borderless menu" ng-repeat-start="question in book[page] | filter:{parent_answer_id:false, parent_question_id:false}">
            <div class="item" ng-if="question.is.type=='radio'"><i class="selected radio icon"></i>單選題</div>
            <div class="item" ng-if="question.is.type=='checkboxs'"><i class="checkmark box icon"></i>複選題</div>
            <div class="item" ng-if="question.is.type=='select'"><i class="arrow circle down icon"></i>下拉選單</div>
            <div class="item" ng-if="question.is.type=='scales'"><i class="ordered list icon"></i>量表題</div>
            <div class="item" ng-if="question.is.type=='texts'"><i class="write icon"></i>文字填答</div>
            <div class="item" ng-if="question.is.type=='list'"><i class="sitemap icon"></i>題組</div>
            <div class="icon link item" ng-click="question.changeType=!question.changeType" ng-if="!question.is.type"><i class="caret right icon"></i></div>
            <div class="active red vertically fitted item" ng-if="question.changeType">
                <md-input-container>
                <label>選擇題型</label>
                <md-select ng-model="question.is.type" ng-change="changeType(question)">
                    <md-option ng-repeat="type in quesTypes | filter:{disabled:'!'}" value="@{{ type.name }}">@{{ type.title }}</md-option>
                </md-select>
                </md-input-container>
            </div>
            <div class="right menu">
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-if="!$first" ng-click="moveRootSort(question, -1)"><i class="caret up icon"></i></div>
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-if="!$last" ng-click="moveRootSort(question, 1)"><i class="caret down icon"></i></div>
                <div class="item" ng-if="question.open.moving">
                    <div class="ui transparent icon input">
                        <input type="text" ng-model="settedPage" placeholder="輸入移動到的頁數...">
                        <i class="share link icon" ng-click="setPage(question, settedPage)"></i>
                    </div>
                </div>
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-click="question.open.moving=!question.open.moving" ng-if="!question.open.moving">
                    <i class="share icon"></i>
                </div>
                <div class="ui dropdown icon item" ng-class="{disabled:question.saving}" ng-click="removeQuestion(question)"><i class="trash icon"></i></div>
            </div>
        </div>

        <div class="ui attached stacked segment" ng-repeat-end ng-class="{loading:question.saving}" ng-mouseenter="question.open.add=true" ng-mouseleave="question.open.add=false">
            <div question="question" sbook="sbook" sbooks="sbooks" page="page" questions="book[page]"></div>
            <a class="ui mini bottom right attached label" ng-if="question.open.add" ng-click="addQuestion($index+1)"><i class="add icon"></i>加入題目</a>
        </div>

    </div>

</div>

<script type="text/ng-template" id="ng-question">
    @include('files.interview.template_editor')
</script>

<script src="/js/angular-file-upload.min.js"></script>
<script src="/js/ckeditor/4.4.7-basic-source/ckeditor.js"></script>
<!--<script src="/js/textAngular/ng-ckeditor.js"></script>-->

<script type="text/ng-template" id="ng-list"></script>

<script>
app.requires.push('angularify.semantic.dropdown');
app.requires.push('angularFileUpload');
app.controller('editorController', function($http, $scope, $sce, $interval, $filter) {

    $scope.book = [];
    $scope.page = 1;
    $scope.order = 0 ;
    $scope.select =[];
    $scope.opening = {books: false};
    $scope.loading = false;

    $scope.quesTypes = [
        {name: 'explain', title: '文字標題'},
        {name: 'select', title: '單選題(下拉式)'},
        {name: 'radio', title: '單選題(點選)'},
        {name: 'checkboxs', title: '複選題'},
        {name: 'scales', title: '量表'},
        {name: 'texts', title: '文字欄位'},
        {name: 'list', title: '題組'},
        {name: 'textarea', title: '文字欄位(大型欄位)', disabled: true},
        {name: 'textscale', title: '文字欄位(表格)', disabled: true},
        {name: 'table', title: '表格', disabled: true}
    ];

    $scope.addQuestion = function(sorter) {
        var question = {
            is: {
                type: '?'
            },
            page: $scope.page,
            sorter: sorter,
            parent_question_id: false,
            parent_answer_id: false,
            changeType : true
        };
        var anchor = $filter('filter')($scope.book[$scope.page], {parent_answer_id:false, parent_question_id:false})[sorter];
        var index = $scope.book[$scope.page].indexOf(anchor);
        if (index >= 0) {
            $scope.book[$scope.page].splice(index, 0, question);
        } else {
            $scope.book[$scope.page].push(question);
        }
    };

    $scope.changeType = function(question) {
        question.changeType = false;
        if (!question.id) {
            $scope.createQuestion(question);
        };
    };

    $scope.createQuestion = function(question) {
        question.saving = true;
        $http({method: 'POST', url: 'createQuestion', data:{sbook: $scope.sbook, question: question}})
        .success(function(data, status, headers, config) {
            console.log(data);
            angular.extend(question, data.question);
            question.saving = false;
            question.searching = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.removeQuestion = function(question) { //appear in directive
        if (!question.id) {
            $scope.book[$scope.page].splice($scope.book[$scope.page].indexOf(question), 1);
            return;
        }
        question.saving = true;
        $http({method: 'POST', url: 'removeQuestion', data:{question: question}})
        .success(function(data, status, headers, config) {
            console.log(data);
            if (data.deleted) {
                $scope.book[$scope.page].splice($scope.book[$scope.page].indexOf(question), 1);
            };
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.moveRootSort = function(question, offset) {
        var questions = $filter('filter')($scope.book[$scope.page], {parent_question_id: false, parent_answer_id: false});
        var index = questions.indexOf(question);
        question.sorter = index+offset;
        question.saving = true;
        $http({method: 'POST', url: 'moveRootSort', data:{sbook: $scope.sbook, question: question}})
        .success(function(data, status, headers, config) {
            console.log(data);
            var target = $scope.book[$scope.page].indexOf(questions[index+offset]);
            $scope.book[$scope.page].splice($scope.book[$scope.page].indexOf(question), 1);
            $scope.book[$scope.page].splice(target, 0, data.question);
            question.saving = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getBooks = function() {
        $http({method: 'POST', url: 'getBooks', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.sbooks = data.sbooks;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getQuestions = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'getEditorQuestions', data:{}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.sbook = data.sbook;
            $scope.book = data.book;
            $scope.page = 1;
            $scope.book[1] = $scope.book[1] || [];
            $scope.lastPage = Object.keys($scope.book)[Object.keys($scope.book).length-1];
            $scope.loading = false;
        }).error(function(e) {
            console.log(e);
        });
    };

    $scope.getBooks();
    $scope.getQuestions();

    $scope.nextPage = function() {
        $scope.book[++$scope.page] = $scope.book[$scope.page] || [];
    };

    $scope.prevPage = function() {
        if ($scope.page > 1) {
            $scope.book[--$scope.page] = $scope.book[$scope.page] || [];
        }
    };

    $scope.setPage = function(question, page) {
        if (parseInt(page) != page) return false;
        question.saving = true;
        $http({method: 'POST', url: 'setPage', data:{question: question, page: page}})
        .success(function(data, status, headers, config) {
            console.log(data);
            $scope.book[page] = $scope.book[page] || [];
            angular.forEach(data.changeds, function(changed) {
                angular.forEach($scope.book[question.page], function(pQuestion) {
                    if (changed.id == pQuestion.id) {
                        $scope.book[pQuestion.page].splice($scope.book[pQuestion.page].indexOf(pQuestion), 1);
                        $scope.book[page].push(changed);
                    };
                });
            });
            console.log($scope.book[page]);
        }).error(function(e) {
            console.log(e);
        });
    };

})

.directive('question', function($compile, FileUploader, $templateCache){
    return {
        restrict: 'A',
        replace: true,
        transclude: false,
        scope: {
            question: '=',
            sbook: '=',
            sbooks:'=',
            page: '=',
            questions: '='
        },
        compile: function(tElement, tAttr) {
            var contents = tElement.contents().remove();
            var compiledContents;

            return function(scope, iElement, iAttr) {
                if (!compiledContents) {
                    compiledContents = [];
                    compiledContents['list'] = $compile($templateCache.get('ng-list'));
                    compiledContents['question'] = $compile($templateCache.get('ng-question'));
                }
                compiledContents['question'](scope, function(clone, scope) {
                    iElement.append(clone);
                });
            };
        },
        controller: function($scope, $http, $interval, $timeout, $filter) {

            $scope.saveTitleNgOptions = {updateOn: 'default blur', debounce:{default: 2000, blur: 0}};
            $scope.searchLoaded = '';
            $scope.searchText = {};

            $scope.icons = {
                radio: {icon: 'selected radio', title: '單選題'},
                select: {icon: 'arrow circle down', title: '下拉選單'},
                checkboxs: {icon: 'checkmark box', title: '複選題'},
                scales: {icon: 'ordered list', title: '量表題'},
                texts: {icon: 'write', title: '文字填答'},
                list: {icon: 'sitemap', title: '題組'}
                // textarea: {icon: 'write square', title: '文字填答(多行)'}
            };

            $scope.removeQuestion = function(question) {
                if (!question.id) {
                    $scope.questions.splice($scope.questions.indexOf(question), 1);
                    return;
                }
                question.saving = true;
                $http({method: 'POST', url: 'removeQuestion', data:{question: question}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    if (data.deleted) {
                        $scope.questions.splice($scope.questions.indexOf(question), 1);
                    };
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.removeAnswer = function(answer) {
                answer.saving = true;
                $http({method: 'POST', url: 'removeAnswer', data:{answer: answer}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    if (data.deleted) {
                        $scope.question.answers.splice($scope.question.answers.indexOf(answer), 1);
                        // angular.forEach(data.childrens, function(children) {
                        //     $filter('filter')();
                        // });
                    };
                }).error(function(e) {
                    console.log(e);
                });
            }

            $scope.getPoolQuestions = function(position) {
                var sBooks = $filter('filter')($scope.sbooks, {checked: true});
                console.log(angular.toJson(sBooks));
                if ($scope.searchLoaded != angular.toJson(sBooks)) {
                    $http({method: 'POST', url: 'getPoolQuestions', data:{type: $scope.question.is.type, sBooks: sBooks}})
                    .success(function(data, status, headers, config) {
                        console.log(data);
                        $scope.pQuestions = data.questions;
                        $scope.question.searching = position;
                        $scope.searchLoaded == angular.toJson(sBooks);
                    }).error(function(e) {
                        console.log(e);
                    });
                };
            };

            $scope.setPoolQuestion = function(pQuestion) {
                $scope.question.saving = true;
                $scope.question.searching = false;
                $scope.searchText = {};
                if ($scope.question.parent_question_id) {
                    $scope.setPoolBranchNormalQuestion(pQuestion);
                };

                if ($scope.question.parent_answer_id) {
                    $scope.setPoolChildrenQuestion(pQuestion);
                };

                if (!$scope.question.parent_question_id && !$scope.question.parent_answer_id) {
                    $scope.setPoolRootQuestion(pQuestion);
                };
            };

            $scope.setPoolRootQuestion = function(pQuestion) {
                var roots = $filter('filter')($scope.questions, {parent_answer_id:false, parent_question_id:false});
                pQuestion.page = $scope.page;
                pQuestion.sorter = roots.indexOf($scope.question);
                pQuestion.parent_answer_id = $scope.question.parent_answer_id || null;
                $http({method: 'POST', url: 'setPoolRootQuestion', data:{sbook: $scope.sbook, pQuestion: pQuestion}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    angular.extend($scope.question, data.sQuestion);
                    angular.forEach(data.csQuestions, function(csQuestion) {
                        $scope.questions.push(csQuestion);
                    });
                    $scope.question.saving = false;
                    $scope.question.changeType = false;
                    $scope.question.open = {questions: true, answers: true};
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.setPoolChildrenQuestion = function(pQuestion) {
                pQuestion.page = $scope.page;
                pQuestion.sorter = $scope.question.sorter;
                pQuestion.parent_answer_id = $scope.question.parent_answer_id;
                $http({method: 'POST', url: 'setPoolRootQuestion', data:{pQuestion: pQuestion}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    angular.extend($scope.question, data.sQuestion);
                    angular.forEach(data.csQuestions, function(csQuestion) {
                        $scope.questions.push(csQuestion);
                    });
                    $scope.question.saving = false;
                    $scope.question.changeType = false;
                    $scope.question.open = {questions: true, answers: true};
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.setPoolBranchNormalQuestion = function(pQuestion) {
                $http({method: 'POST', url: 'setPoolBranchNormalQuestion', data:{bQuestion: $scope.question, pQuestion: pQuestion}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    angular.extend($scope.question, data.question);
                    angular.forEach(data.bbQuestions, function(question) {
                        $scope.questions.push(question);
                    });
                    $scope.question.saving = false;
                    $scope.question.changeType = false;
                    $scope.question.open = {questions: true, answers: true};
                }).error(function(e) {
                    console.log(e);
                });
            }

            $scope.setPoolScaleBranchQuestion = function(pQuestion) {
                pQuestion.page = $scope.page;
                $http({method: 'POST', url: 'setPoolScaleBranchQuestion', data:{question: $scope.question, pQuestion: pQuestion}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    angular.forEach(data.questions, function(question) {
                        $scope.questions.push(question);
                    });
                    $scope.question.saving = false;
                    $scope.question.changeType = false;
                    $scope.question.open = {questions: true, answers: true};
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.createAnswer = function(question) {
                $http({method: 'POST', url: 'createAnswer', data:{question: question}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    question.answers.push(data.answer);
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.saveQuestionTitle = function(question) {
                question.saving = true;
                $http({method: 'POST', url: 'saveQuestionTitle', data:{question: question}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    question.is.title = data.title;
                    question.saving = false;
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.saveAnswerTitle = function(answer) {
                answer.saving = true;
                $http({method: 'POST', url: 'saveAnswerTitle', data:{answer: answer}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    answer.is.title = data.title;
                    answer.saving = false;
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.addrQuestion = function(question, type) {
                var csQuestion = {
                    is: {
                        type: type
                    },
                    page: $scope.page,
                    sorter: $filter('filter')($scope.questions, {parent_question_id: question.id}).length,
                    parent_question_id: question.id
                };
                $http({method: 'POST', url: 'addrQuestion', data:{sbook: $scope.sbook, question: csQuestion}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    $scope.questions.push(data.csQuestion);
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.addcQuestion = function(answer) {
                var csQuestion = {
                    is: {
                        type: '?'
                    },
                    page: $scope.page,
                    sorter: $filter('filter')($scope.questions, {parent_answer_id: answer.id}).length,
                    parent: answer,
                    parent_answer_id: answer.id
                };
                $scope.questions.push(csQuestion);
            };

            $scope.addBranchQuestion = function(index) {
                var bQuestions = $filter('filter')($scope.questions, {parent_question_id: $scope.question.id});
                var nQuestion = {
                    is: {
                        type: '?'
                    },
                    page: $scope.page,
                    sorter: index,
                    parent_question_id: $scope.question.id
                };
                $scope.questions.splice($scope.questions.indexOf(bQuestions[index]), 0, nQuestion);
            };

            $scope.moveBranchSort = function(question, offset) {
                var questions = $filter('filter')($scope.questions, {parent_question_id: question.parent_question_id});
                var index = questions.indexOf(question);
                question.sorter = index+offset;
                question.saving = true;
                $http({method: 'POST', url: 'moveBranchSort', data:{question: question}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    var target = $scope.questions.indexOf(questions[index+offset]);
                    $scope.questions.splice($scope.questions.indexOf(question), 1);
                    $scope.questions.splice(target, 0, question);
                    question.saving = false;
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.moveChildrenSort = function(question, offset) {
                var questions = $filter('filter')($scope.questions, {parent_question_id: false, parent_answer_id: question.parent_answer_id});
                var index = questions.indexOf(question);
                question.sorter = index+offset;
                question.saving = true;
                $http({method: 'POST', url: 'moveChildrenSort', data:{question: question}})
                .success(function(data, status, headers, config) {
                    console.log(data);
                    var target = $scope.questions.indexOf(questions[index+offset]);
                    $scope.questions.splice($scope.questions.indexOf(question), 1);
                    $scope.questions.splice(target, 0, question);
                    question.saving = false;
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.save_img_db = function(ques_id, path) {
                var data = {ques_id:ques_id, path:path}
                $http({method: 'POST', url: 'save_img_db', data:data })
                .success(function(data, status, headers, config) {
                    console.log(data);
                }).error(function(e) {
                    console.log(e);
                });
            };

            $scope.uploader = new FileUploader({
                alias: 'CDBimg',
                url: 'img_upload',
                autoUpload: true,
                formData: $scope.question.id
            });

            $scope.uploader.onAfterAddingFile = function(fileItem) {
                $scope.item = fileItem;
                $scope.progress = 0;
            };

            $scope.uploader.onProgressItem = function(fileItem, progress) {
                $scope.progress = fileItem.progress;
            };

            $scope.uploader.onErrorItem = function(fileItem, response, status, headers) {
                $scope.error = fileItem.isError == true;
            };

            $scope.uploader.onCompleteItem  = function(fileItem, response, status, headers) {
                //console.log(array_key_exists('CDBimg', response));
                if(response.result == 1){
                    $scope.success = fileItem.isSuccess == true;
                    $scope.save_img_db(fileItem.formData, response.path);
                }
                else{
                    if(status == 500) {
                        $scope.overSize = null;
                    }
                    else{
                        $scope.overSize = response.CDBimg[0];
                    }
                }
                $scope.uploader.destroy();
            };

            $scope.ques_import_var = function(question) {
                question.answers.length = 0;
                var list = question.importText.split('\n');
                for(index in list){
                    var itemn = list[index].split(' ');
                    question.answers.push({value:itemn[0], title:itemn[1]});
                }
                question.code = 'manual';
                question.importText = null;
                question.is_import = false;
            };

        }
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
            element.bind('blur', function() {
                ngModel.$setViewValue(element.html());
            });
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
