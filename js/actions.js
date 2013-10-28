/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $(document).on('change', 'select', function(){
    $('#icon-preview').removeClass().addClass($('select').val() + ' ReactSprite');
  });
});
