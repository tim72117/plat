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