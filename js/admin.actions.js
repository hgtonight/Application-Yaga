/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('#Actions').sortable({
    axis: 'y',
    containment: 'parent',
    cursor: 'move',
    cursorAt: {left: '10px'},
    forcePlaceholderSize: true,
    items: 'li',
    placeholder: 'Placeholder',
    opacity: .6,
    tolerance: 'pointer',
    update: function() {
      $.post(
        gdn.url('/yaga/action/sort.json'),
        {
          'SortArray': $('ol.Sortable').sortable('toArray'),
          'TransientKey': gdn.definition('TransientKey')
        },
        function(response) {
          if (!response || !response.Result) {
            alert("Oops - Didn't save order properly");
          }
        }
      );
    }
  });
  // find the existing class to select
//  var CurrentCssClass = $("input[name='CssClass']").val();
//
//  if(CurrentCssClass.length) {
//    $("#ActionIcons img[data-class='" + CurrentCssClass + "']").addClass('Selected');
//  }

  // If someone types in the class manually, deselect icons and select if needed
  $(document).on('input', "input[name='CssClass']", function() {
    $('#ActionIcons img.Selected').removeClass('Selected');

    var FindCssClass = $(this).val();
    if(FindCssClass.length) {
      $("#ActionIcons img[data-class='" + CurrentCssClass + "']").addClass('Selected');
    }
  });

  $(document).on('click', '#ActionIcons img', function() {
    var newCssClass = 'React' + $(this).attr('title');
    $("input[name='CssClass']").val(newCssClass);
    $('#ActionIcons img.Selected').removeClass('Selected');
    $(this).addClass('Selected');
  });
});
