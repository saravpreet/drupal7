<?php

/**
 * Create classes which are used for schema introspection.
 */
class CRM_Core_CodeGen_Reflection extends CRM_Core_CodeGen_BaseTask {

  protected $checksum;

  /**
   * @var string
   */
  private $raw;

  /**
   * @return bool
   *   TRUE if an update is needed.
   */
  public function needsUpdate() {
    if (!file_exists($this->getAbsFileName())) {
      return TRUE;
    }

    // Generating this file is fairly cheap, and we don't have robust heuristic
    // for the checksum.
    //    if ($this->getSchemaChecksum() !== self::extractRegex($this->getAbsFileName(), ';\(GenCodeChecksum:([a-zA-Z0-9]+)\);')) {
    //      return TRUE;
    //    }

    return !$this->isApproxPhpMatch(
      file_get_contents($this->getAbsFileName()),
      $this->getRaw());
  }


  /**
   * Run generator.
   */
  public function run() {
    echo "Generating table list\n";
    $template = new CRM_Core_CodeGen_Util_Template('php');
    $template->assign('tables', $this->tables);
    $template->assign('genCodeChecksum', 'IGNORE');
    $template->run('listAll.tpl', $this->getAbsFileName());
  }

  /**
   * Generate the raw PHP code for the data file.
   *
   * @return string
   */
  public function getRaw() {
    if (!$this->raw) {
      $template = new CRM_Core_CodeGen_Util_Template('php');
      $template->assign('tables', $this->tables);
      $template->assign('genCodeChecksum', 'NEW');
      $this->raw = $template->fetch('listAll.tpl');
    }
    return $this->raw;
  }

  /**
   * Get absolute file name.
   *
   * @return string
   */
  protected function getAbsFileName() {
    return $this->config->CoreDAOCodePath . "AllCoreTables.data.php";
  }

  //   /**
  //    * Get the checksum for the schema.
  //    *
  //    * @return string
  //    */
  //   protected function getSchemaChecksum() {
  //     if (!$this->checksum) {
  //       CRM_Utils_Array::flatten($this->tables, $flat);
  //       ksort($flat);
  //       $this->checksum = md5(json_encode($flat));
  //     }
  //     return $this->checksum;
  //   }

}
