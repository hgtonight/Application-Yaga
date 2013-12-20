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
        gdn.url('action/sort.json'),
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

  // Wait to hide things after a popup reveal has happened
  $('body').on('popupReveal', function() {

    // Hide the advanced settings
    $('#AdvancedActionSettings').children('div').hide();
    $('#AdvancedActionSettings span').click(function(){
      $(this).siblings().slideToggle();
    });

    // If someone types in the class manually, deselect icons and select if needed
    $("input[name='CssClass']").on('input', function() {
      $('#ActionIcons .reaction.Selected').removeClass('Selected');

      var FindCssClass = $(this).val();
      if(FindCssClass.length) {
        $("#ActionIcons .reaction[data-icon='" + CurrentCssClass + "']").addClass('Selected');
      }
    });

    $('#ActionIcons .reaction').click(function() {
      var newCssClass = 'reaction-' + $(this).attr('title');
      $("input[name='CssClass']").val(newCssClass);
      $('#ActionIcons .reaction.Selected').removeClass('Selected');
      $(this).addClass('Selected');
    });
  });

  // If the form is already existing, trigger the event manually
  if($('#AdvancedActionSettings').length) {
    $('body').trigger('popupReveal');
  }
});
