$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#lots').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#lots').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'number'},
      {'data': 'quantity'},
      {
        'data': 'wip_title',
        'orderable': false
      },
      {
        'data': 'processes',
        'orderable': false
      },
      {'data': 'created_at'},
      {'data': 'updated_at'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

  $("#lots_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#lots').DataTable().columns(i).search(v).draw();
  });

  $('#line_id').change(function(){
    console.log("change law");
    var params = "line_id="+$(this).val();
    var fullUrl = window.location.href;
    var index = fullUrl.lastIndexOf("/");
    var urlPath = fullUrl.substring(0, index)+"/";
    $.ajax({
      type: "GET",
      url: urlPath+"lot/product-select",
      dataType: "html",
      data: params,
      cache: false,
      success: function(data){
        $("#product-choose").html(data);
      }
    });

    $.ajax({
      type: "GET",
      url: urlPath+"lot/process-select",
      dataType: "html",
      data: params,
      cache: false,
      success: function(data){
        $("#process-choose").html(data);
      }
    });
  });

});