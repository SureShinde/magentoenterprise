#/**
# * Nucleus
# *
# * NOTICE OF LICENSE
# *
# * This source file is subject to the Nucleus License
# * that is bundled with this package in the file LICENSE_NUCLEUS.txt.
# * It is also available through the World Wide Web at this URL:
# * http://www.nucleuscommerce.com/license
# * If you did not receive a copy of the license and are unable to
# * obtain it through the World Wide Web, please send an email
# * to support@nucleuscommerce.com so we can send you a copy immediately.
# *
# * DISCLAIMER
# *
# * Do not edit or add to this file if you wish to upgrade Nucleus
# * to newer versions in the future.
# *
# * @category   CLS
# * @package    NucleusCoreEnterprise
# * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
# * @license    http://www.nucleuscommerce.com/license
# */

# Supersedes the version in scss in core codebase, so relative paths still work

# note: this should never truly be referenced since we are using relative assets
http_path = "/skin/frontend/rwd/enterprise/"
css_dir = "css"
sass_dir = "scss"
images_dir = "images"
javascripts_dir = "js"
relative_assets = true

add_import_path "../default/scss"

output_style = :expanded
environment = :production
# line_comments = false