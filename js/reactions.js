/* Copyright 2013 Zachary Doll */
jQuery(document).ready(function($) {
  $('.Expander').expander({slicePoint: 200, expandText: gdn.definition('ExpandText'), userCollapseText: gdn.definition('CollapseText')});
});
