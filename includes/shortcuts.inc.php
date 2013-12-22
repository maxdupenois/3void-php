<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**

 * @author Max
 */
//Get current page
function me(){
    return $GLOBALS['CURRENT_PAGE'];
}


//Host based paths
function host($link=""){
    return $GLOBALS['DOMAIN']."/".$link;
}
function css($link=""){
    return host($GLOBALS['FOLDER_CSS']."/".$link);
}
function js($link=""){
    return host($GLOBALS['FOLDER_JS']."/".$link);
}
function images($link=""){
    return host($GLOBALS['FOLDER_IMAGES']."/".$link);
}
function admin($link=""){
    return host($GLOBALS['FOLDER_ADMIN']."/".$link);
}
function uploads($link=""){
    return host($GLOBALS['FOLDER_UPLOADS']."/".$link);
}
//Util functions to echo host based paths
function _link($link=""){
    echo host($link);
}
function _css($link=""){
    echo css($link);
}
function _js($link=""){
    echo js($link);
}
function _img($link=""){
    echo img($link);
}
function _admin($link=""){
    echo admin($link);
}
function _uploads($link=""){
    echo uploads($link);
}


/*Extensions*/
function exta($link){
    return $link.".".$GLOBALS['ACTION_EXTENSION'];
}
function extp($link){
    return $link.".".$GLOBALS['PAGE_EXTENSION'];
}
function _a($link){
    echo exta(host($link));
}
function _p($link){
    echo extp(host($link));
}
/*DEBUG*/
function isDebug(){
    return $GLOBALS['DEBUG'];
}
/*Form Shortcuts*/
function frm(){
    if(frmExists()){
        return $GLOBALS['FORM_RESULTS'];
    }else{
        return null;
    }
}
function frmExists(){
    return (isset($GLOBALS['FORM_RESULTS']));
}
function formVal($key){
    if(frmExists()&&frm()->hasValueFor($key)){
        return frm()->getValue($key);
    }
    return "";
}
function _formVal($key){
    echo formVal($key);
}


/*Actual Document Locations*/
function path($path=""){
    return $GLOBALS['SITE_ROOT']."/".$path;
}

function inc($path=""){
    return path($GLOBALS['FOLDER_INCLUDES']."/".$path);
}
function clasz($path=""){
    return path($GLOBALS['FOLDER_CLASS']."/".$path);
}
function tmplt($path=""){
    return path($GLOBALS['FOLDER_TEMPLATES']."/".$path);
}
function temp($path=""){
    return path($GLOBALS['FOLDER_TEMP']."/".$path);
}
function act($path=""){
    return path($GLOBALS['FOLDER_ACTIONS']."/".$path);
}
function p_uploads($path=""){
    return path($GLOBALS['FOLDER_UPLOADS']."/".$path);
}


function _part($path=""){
    include(tmplt($path));
}
/*TINY MCE*/
function hasTinymce(){
    return (is_file(path($GLOBALS['TINY_MCE_LOCATION'])));
}

function _tinymceloc(){
    echo $GLOBALS['TINY_MCE_LOCATION'];
}
function _tinymcecss(){
    echo $GLOBALS['TINY_MCE_TEXT_CSS'];
}
?>
