<<<<<<< Updated upstream
<script src="/js/ngHandsontable.min.js"></script>
<script src="/js/handsontable.full.min.js"></script>
<link rel="stylesheet" media="screen" href="/js/handsontable.full.min.css">

<div ng-controller="Ctrl">
<hot-table
    settings="{colHeaders: colHeaders, contextMenu: ['row_above', 'row_below', 'remove_row'], afterChange: afterChange }"
    rowHeaders="false"
    minSpareRows="minSpareRows"
    datarows="db.items"
    height="300"
    width="700">
    <hot-column data="id"                  title="'ID'"></hot-column>
    <hot-column data="name.first"          title="'First Name'"  type="grayedOut"  readOnly></hot-column>
    <hot-column data="name.last"           title="'Last Name'"   type="grayedOut"  readOnly></hot-column>
    <hot-column data="address"             title="'Address'" width="150"></hot-column>
    <hot-column data="product.description" title="'Favorite food'" type="'autocomplete'">
        <hot-autocomplete datarows="description in product.options"></hot-autocomplete>
    </hot-column>
    <hot-column data="price"               title="'Price'"     type="'numeric'"  width="80"  format="'$ 0,0.00'" ></hot-column>
    <hot-column data="isActive"            title="'Is active'" type="'checkbox'" width="65"  checkedTemplate="'Yes'" uncheckedTemplate="'No'"></hot-column>
</hot-table>
</div>

<script>
angular.module('app', ['ngHandsontable']).controller('Ctrl', Ctrl);

function Ctrl($scope, $filter) {
    $scope.db = {};
   $scope.db.items = [
    {
      "id":1,
      "name":{
        "first":"John",
        "last":"Schmidt"
      },
      "address":"45024 France",
      "price":760.41,
      "isActive":"Yes",
      "product":{
        "description":"Fried Potatoes",
        "options":[
          {
            "description":"Fried Potatoes",
            "image":"//a248.e.akamai.net/assets.github.com/images/icons/emoji/fries.png",
            "Pick$":null
          },
          {
            "description":"Fried Onions",
            "image":"//a248.e.akamai.net/assets.github.com/images/icons/emoji/fries.png",
            "Pick$":null
          }
        ]
      }
    }
    //more items go here
  ];
  console.log($scope.db);
}

</script>
=======
<div ng-controller="MyCtrl">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
                <th ng-click="predicate = ['name','des']; reverse=true">Name(click me to order)></th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="name in (names | orderBy:predicate:reverse )">
              <td>{{name.x}}</td>
              <td>{{name.name}}</td>
              <td>{{name.des}}</td>
            </tr>
          </tbody>
        </table>
</div>
<script>
angular.module('app', []).filter("emptyToEnd", function () {
    return function (array, key) {
        if (!angular.isArray(array)) return;
        if (!angular.isArray(key)) return array;
        console.log(array);
        var present = array.filter(function (item) {
            return item[key[0]];
        });
        var empty = array.filter(function (item) {
            return (!item[key[0]] && item[key[0]]!=0);
        });
        var zero = array.filter(function (item) {
            return (!item[key[0]] && item[key[0]]==0);
        });
        console.log(key);
        var step = present.concat(zero);
        return step.concat(empty);
    };
}).controller('MyCtrl', MyCtrl)
function MyCtrl($scope) {
    
    $scope.names = [
        {"x":1,"name":'',"des":"DNP"},
        {"x":2,"name":'',"des":"DNP"},
        {"x":3,"name":'',"des":"DNP"},
        {"x":4,"name":-1,"des":1},
        {"x":5,"name":'20',"des":1},
        {"x":6,"name":'100',"des":1},
        {"x":7,"name":10,"des":1},
        {"x":8,"name":10,"des":1},
        {"x":9,"name":'',"des":1},
        {"x":10,"name":0,"des":1},
        {"x":11,"name":'',"des":2}
    ];
}
</script>
<div contenteditable="true" style="width:500px;height:500px;border: 1px solid black"></div>
>>>>>>> Stashed changes
