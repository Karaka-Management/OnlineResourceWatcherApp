/**
 * Karaka
 *
 * @package   App
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */

#include <stdio.h>
#include <stdlib.h>
#include <regex.h>

#include "cOMS/Utils/ArrayUtils.h"
#include "cOMS/Utils/FileUtils.h"
#include "cOMS/Utils/WebUtils.h"
#include "cOMS/Hash/MeowHash.h"
#include "cOMS/Utils/Parser/Json.h"
#include "Stdlib/HashTable.h"

#include "Models/Db.h"
#include "Models/InstallType.h"

#ifndef OMS_DEMO
    #define OMS_DEMO false
#endif

void (*f_ptr)(int, char **);

void printHelp(int argc, char **argv)
{
    printf("    The Online Resource Watcher app developed by jingga checks online or local resources\n");
    printf("    for changes and and informs the user about them.\n\n");
    printf("    Run: ./App ....\n\n");
    printf("    -h: Prints the help output\n");
    printf("    -v: Prints the version\n");
    printf("\n");
    printf("    Website: https://jingga.app\n");
    printf("    Copyright: jingga (c) Dennis Eichhorn\n");
}

void printVersion()
{
    printf("Version: 1.0.0\n");
}

void install(Models::InstallType type = Models::InstallType::LOCAL)
{
    if (type == Models::InstallType::LOCAL) {
        // create sqlite database

    } else {

    }

    // create config file
    nlohmann::json config;
}

void parseConfigFile()
{
    FILE *fp = fopen("config.json");

    nlohmann::json config = nlohmann::json::parse(fp);
}

inline
bool isResourceDateModified(char *filename, time_t lastChange)
{
    return oms_abs(Utils::FileUtils::last_modification(filename) - lastChange) > 1;
}

inline
bool hasResourceContentChanged(Utils::FileUtils::file_body f1, Utils::FileUtils::file_body f2)
{
    Hash::Meow::meow_u128 h1 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f1.size, f1.content);
    Hash::Meow::meow_u128 h2 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f2.size, f2.content);

    return Hash::Meow::MeowHashesAreEqual(h1, h2);
}

Utils::FileUtils::file_body hasChanged(char *oldResource, char *newResource, time_t lastChange)
{
    char *t;
    int length = 0;

    for (t = newResource; *t != '\0' && length < 7; ++t) {
        ++length;
    }

    Utils::FileUtils::file_body f1;
    Utils::FileUtils::file_body f2;

    bool isFileModified = false;
    if (length > 5
        && (strncmp(newResource, "https:", 6) || strncmp(newResource, "www.", 4))
    ) {
        // web resource
        f1 = Utils::FileUtils::read_file(oldResource);
        f2 = Utils::WebUtils::download(newResource);
    } else {
        // local resource
        isFileModified = isResourceDateModified(oldResource, lastChange);
        if (isFileModified) {
            f1 = Utils::FileUtils::read_file(oldResource);
            f2 = Utils::FileUtils::read_file(newResource);
        }
    }

    bool hasChanged = isFileModified || hasResourceContentChanged(f1, f2);

    free(f1.content);

    if (hasChanged) {
        free(f2.content);
        f2.size = -1;
    }

    return f2;
}

void saveResourceChange(char *url, char *oldResource)
{
    Utils::FileUtils::file_body dowloadData = Utils::WebUtils::download(url);

    Utils::FileUtils::file_body fileData = Utils::FileUtils::read_file(oldResource);
}

int main(int argc, char **argv)
{
    bool hasHelpCmd = Utils::ArrayUtils::has_arg("-h", argv, argc);
    if (hasHelpCmd) {
        printHelp(argc, argv);

        return 0;
    }

    bool hasVersionCmd = Utils::ArrayUtils::has_arg("-v", argv, argc);
    if (hasVersionCmd) {
        printVersion();

        return 0;
    }

    f_ptr = &printHelp;

    Stdlib::HashTable::ht *table = Stdlib::HashTable::create_table();
    if (table == NULL) {
        return -1;
    }

    Stdlib::HashTable::set_entry(table, "-h", &printHelp);

    regex_t regex;
    regcomp(&regex, "\-h", 0);
    regexec(&regex, argv[0], 0, NULL, 0) == 0;

    // @todo handle install
    // create config
    // check install type
        // web = copy config from web
        // local
            // create sqlite db
            // create config from template

    if (!Utils::FileUtils::file_exists("config.json")) {
        printf("No config file available.");

        return -1;
    }

    unsigned long resourceId = (unsigned long) Utils::ArrayUtils::get_arg("-r", argv, argc);

    // @todo handle resources
    // load config
    // get resources
        // active
        // last check older than 23 h
    // check if resource changed
        // save new version
        // find differences
    // inform users

    Resource res[10];

    Stdlib::HashTable::free_table(table);
}
