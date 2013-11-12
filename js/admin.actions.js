/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  
  // Update preview icon for reactions on admin page
  $(document).on('change', 'select', function(){
    $('#icon-preview').removeClass().addClass($('select').val() + ' ReactSprite');
  });
  
});
