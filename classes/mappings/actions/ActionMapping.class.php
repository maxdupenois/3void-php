<?php
/**
 * Dummy class to send to an action, needs no database table as it has no values
 */
class ActionMapping extends Mapping {
    public function __construct($uri) {
        parent::__construct($uri);
    }

    public function setAction($action) {
        $this->action = $action;
        $this->setIsAction(true);
    }

    public function getAction() {
        return $this->action;
    }

    public static function getActionMapping($uri, $purpose = 'view') {
        $db = $GLOBALS['DATABASE'];

        $uri = Mapping::stripURIExtension($uri);
        $actionMapping = new ActionMapping($uri);


        $actionMapping->fillMapping();

        if($actionMapping==NULL && $purpose == 'view') {
            Page::pageNotFound();
        }else if($actionMapping==NULL && $purpose != 'view') {
            return NULL;
        }
        if(!Authorisation::authoriseMapping($actionMapping, $purpose)) {
            if($purpose=="view"){
                Page::unauthorised($uri);
            }else{
                $GLOBALS['FORM_RESULTS']->addErr("authorisation", "You do not have permission to get this mapping");
                return NULL;
            }
        }

        //Strip extension
        $uri = Mapping::stripURIExtension($uri);
        $uri = ($actionMapping->isRegex()?$actionMapping->getRegex():$uri);

        $sql = "SELECT `action-id` FROM `mappings-actions` WHERE uri = \"%s\"";
        $sqlAction = $db->query($sql, $uri);
        $actionId = null;
        if(($a=array_pop($sqlAction))!=null) $actionId = $a->{'action-id'};

        //Get action
        if($actionId == null&&$purpose=="view") {
            $GLOBALS['REQUESTED_URI'] = $uri;
            Page::internalServerError("Action cannot be found for ".$uri);
        }else if($actionId == null&&$purpose!="view"){
            $GLOBALS['FORM_RESULTS']->addErr("action", "Action cannot be found for ".$uri);

        }else{
            $action = Action::getAction($actionId);
            if($action == null&&$purpose=="view") {
                $GLOBALS['REQUESTED_URI'] = $uri;
                Page::internalServerError("Action cannot be found for id ".$actionId);
            }else if($action == null&&$purpose!="view"){
                $GLOBALS['FORM_RESULTS']->addErr("action", "Action cannot be found for id ".$actionId);
            }else{
                $actionMapping->setAction($action);#
            }
        }
        return $actionMapping;
    }

    public function delete() {
        if(!Authorisation::isAuthorised("admin.action.create")) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }

        if(!parent::delete())return false;


        $db = $GLOBALS['DATABASE'];

        $sql = "DELETE FROM `mappings-actions` WHERE `uri`=\"%s\"";
        $db->query($sql, $this->getUri());

        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Action mapping '".$this->getTitle()
            ." -- ".$this->getUri()."' has been deleted");
        return true;
    }

    public function add() {
        if(!Authorisation::isAuthorised("admin.action.create")) {
            $GLOBALS['FORM_RESULTS']->addErr("unauthorised", "You do not have permission to do this");
            return false;
        }
        if($this->getAction() == NULL || $this->getAction() == "") {
            $GLOBALS['FORM_RESULTS']->addErr("action", "No action given");
            return false;
        }

        if(!parent::add())return false;


        $db = $GLOBALS['DATABASE'];

        $sql = "INSERT INTO `mappings-actions` (uri, `action-id`) "
            ."VALUES (\"%s\", %d)";
        $db->query($sql, $this->getUri(), $this->getAction()->getId());

        $GLOBALS['FORM_RESULTS']->clear();
        $GLOBALS['FORM_RESULTS']->addMsg("success", "Action Mapping '".$this->getTitle()
            ." -- ".$this->getUri()."' has been added");
        return true;
    }


    
    public static function getActionMappingFolderListing() {
        return Listing::listingByFolder(ActionMapping::getListing(true), "getUri()");
    }

}
?>