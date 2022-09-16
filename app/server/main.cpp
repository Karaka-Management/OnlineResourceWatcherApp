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

#include <mysql.h>
#include <curl/curl.h>

#include "cOMS/Utils/ArrayUtils.h"
#include "cOMS/Utils/FileUtils.h"
#include "cOMS/Hash/MeowHash.h"
#include "cOMS/Utils/Parser/Json.h"

#include "Models/Db.h"

#ifndef OMS_DEMO
    #define OMS_DEMO false
#endif

void printHelp()
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

void parseConfigFile()
{
    FILE *fp = fopen("config.json");

    nlohmann::json config = nlohmann::json::parse(fp);
}

bool isResourceModified(char *filename, time_t last_change)
{
    return oms_abs(Utils::FileUtils::last_modification(filename) - last_change) > 1;
}

bool hasResourceContentChanged(char *filename1, char *filename2)
{
    Utils::FileUtils::file_body f1 = Utils::FileUtils::read_file(filename1);
    Utils::FileUtils::file_body f2 = Utils::FileUtils::read_file(filename2);

    Hash::Meow::meow_u128 h1 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f1.size, f1.content);
    Hash::Meow::meow_u128 h2 = Hash::Meow::MeowHash(Hash::Meow::MeowDefaultSeed, f2.size, f2.content);

    bool areHashesEqual = Hash::Meow::MeowHashesAreEqual(h1, h2);

    free(f1.content);
    free(f2.content);

    return areHashesEqual;
}

void saveResourceChange()
{

}

MYSQL *con = null;

int main(int argc, char **argv)
{
    bool hasHelpCmd = Utils::ArrayUtils::has_arg("-h", argv, argc);
    if (hasHelpCmd) {
        printHelp();

        return 0;
    }

    bool hasVersionCmd = Utils::ArrayUtils::has_arg("-v", argv, argc);
    if (hasVersionCmd) {
        printVersion();

        return 0;
    }

    if (!Utils::FileUtils::file_exists("config.json")) {
        printf("No config file available.");

        return -1;
    }

    unsigned long resourceId = (unsigned long) Utils::ArrayUtils::get_arg("-r", argv, argc);


    // read config file
    // create database connection (either mariadb or sqlite)
    // @todo create wrapper for sqlite, mysql and postgresql

    con = mysql_init(NULL);
    if (mysql_real_connect(con, "localhost", "root", "root_passwd", NULL, 0, NULL, 0) == NULL) {
        fprintf(stderr, "%s\n", mysql_error(con));
        mysql_close(con);
        exit(1);
    }
}
