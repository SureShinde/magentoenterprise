<?php

/**
 * Product:       Xtento_ProductExport (1.7.6)
 * ID:            dIaxXi5+TGYgezAgBRd+u1PmprrlxVXXQYb4E6yGO2Y=
 * Packaged:      2016-04-05T21:25:32+00:00
 * Last Modified: 2013-02-10T18:04:46+01:00
 * File:          app/code/local/Xtento/ProductExport/Model/Destination/Interface.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

interface Xtento_ProductExport_Model_Destination_Interface
{
    public function testConnection();
    public function saveFiles($fileArray);
}