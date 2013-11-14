/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  
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
