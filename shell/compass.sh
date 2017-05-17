#!/usr/bin/env bash
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
# * @package    NucleusCore
# * @copyright  Copyright (c) 2015 Nucleus Commerce, LLC (http://www.nucleuscommerce.com)
# * @license    http://www.nucleuscommerce.com/license
# */

## Set bash mode to exit on error
set -e

## Output usage text if asked for
if [[ "$1" = "--help" ]]; then
    echo "usage: compass.sh [--clean]";
    exit;
fi

## Store cwd so we exit with no changes
_cwd="`pwd`"

## Retrieve our fully-qualified dir path
pushd "`dirname "$0"`" > /dev/null
script_dir="`pwd`"
popd > /dev/null

## Full path to design folder
source_dir="$(dirname "$script_dir")/skin/frontend"

# Execute the compass compiler on all themes that contain a config.rb file
# Unfortunately, need to check for a specifically-named directory to make sure we don't have an output error when there's nothing to compile
for dir in $(find $source_dir -mindepth 2 -maxdepth 2 -type d)
do
    if [[ -f "$dir"/config.rb ]] && [[ -d "$dir"/scss ]]; then
        cd "$dir"
        pretty_name=$(basename $(dirname $dir))/$(basename $dir)

        if [[ "$1" == "--clean" ]]; then
            echo " cleaning $pretty_name/.sass-cache/"
            rm -rf "$dir.sass-cache"
        fi

        echo " building $pretty_name"
        compass compile ./
    fi
done

## Reset cwd to stored path
cd "$_cwd"
