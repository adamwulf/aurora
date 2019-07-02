avalanche is a program that manages avalancheModules and avalancheSkins.

CONFIG.PHP
  this file contains configuration information for avalanche.
	root directory etc.
	mysql connect info etc.
  also the location of the skins folder.
  included by [include path]/include.php
  


COMMAND.PHP
  a sample main file of a program







avalanche->functions()
-----------------------------------------------------
available properties to pass in parameter $extra

applicable to functions:
title($str, $extra)
//align

p_title($str, $extra) *
//align

p($str, $extra) *
//align

font($str, $extra)


a($str, $extra) *
//name
//href
//hreflang
//shape
//coords
//tabindex


button($extra) *
//name
//value
//type * not submit
//disabled
//tabindex

check($extra) *
//same as input

hr($extra, $width)
//align
//size

input($extra) *
//name
//value
//size
//maxlength
//alt
//tabindex
//accesskey
//accept
//type * not radio or checkbox

li($str, $extra)
//value

ol($str, $start, $extra)

option($value, $disp, $extra) *
//selected
//disabled
//label

radio($extra) *
//same as input

select($extra) *
//name
//size
//multiple
//disabled
//tabindex

submit($extra) *
//same as button

table($str, $width, $extra) *
//summary
//align
//valign

td($str, $width, $extra) *
//align
//valign
//rowspan
//colspan

textarea($str, $extra)
//name
//rows
//cols
//disabled
//readonly
//tabindex
//accesskey

tr($str, $extra) *
//align
//valign

ul($str, $extra)