$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#parts').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#parts').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {
        'data': 'checkbox',
        'orderable': false
      },
      {'data': 'id'},
      {'data': 'number'},
      {'data': 'name'},
      {
        'data': 'product',
        'orderable': false
      },
      {
        'data': 'process',
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

  $("#parts_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#parts').DataTable().columns(i).search(v).draw();
  });

  $('#product_id').change(function(){
    console.log("change law");
    var params = "product_id="+$(this).val();
    $.ajax({
      type: "POST",
      url: "/admin/part/process-select",
      dataType: "html",
      data: params,
      cache: false,
      success: function(data){
        $("#process-choose").html(data);
      }
    });
  });

  $("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
  });
  $(document).on("click", ".odd, .even", function(event){
    if (event.target.type !== 'checkbox') {
      var obj = $(this).find("input[type=checkbox]");
      var check = obj.is(':checked');
      if(check){
        obj.prop('checked', false);
      } else {
        obj.prop('checked', true);
      }
    }
  });
  $(".btn-delete").click(function(){
    var rows = $('input:checkbox:checked').map(function(){
         return $(this).val();
    }).get();
    if(rows==""){
      alert("Please select at least one record.");
    } else {
      var fullUrl = window.location.href;
      var index = fullUrl.lastIndexOf("/");
      var urlPath = fullUrl.substring(0, index)+"/";
      var url = urlPath+"part/delete-multi?rows="+rows;
      location.href = url;
    }
  });

});