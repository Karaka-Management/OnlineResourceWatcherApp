/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
#ifndef CONTROLLER_INSTALL_H
#define CONTROLLER_INSTALL_H

#include <stdio.h>
#include <stdlib.h>
#include <string>

#include "cOMS/Utils/Parser/Json.h"
#include "cOMS/Utils/ArrayUtils.h"
#include "../cOMS/DataStorage/Database/Connection/ConnectionFactory.h"
#include "../cOMS/DataStorage/Database/Connection/ConnectionAbstract.h"
#include "../cOMS/DataStorage/Database/Connection/DbConnectionConfig.h"

#include "../Models/InstallType.h"

namespace Controller {
    namespace InstallController {
        int installWeb()
        {
            // Create config by copying weg config (nothing else necessary)
            Utils::FileUtils::file_body config = Utils::FileUtils::read_file("../web/config.json");

            FILE *fp = fopen("config.json", "w");
            if (fp == NULL || config.content == NULL) {
                if (config.content != NULL) {
                    free(config.content);
                }

                return -1;
            }

            fwrite(config.content, sizeof(char), config.size, fp);
            fclose(fp);

            free(config.content);

            return 0;
        }

        int installLocal()
        {
            // Create config by copying config template
            FILE *in = fopen("Install/config.json", "r");
            if (in == NULL) {
                return -1;
            }

            nlohmann::json config = nlohmann::json::parse(in);

            std::string strJson = config.dump(4);

            FILE *out = fopen("config.json", "w");
            if (out == NULL) {
                return -1;
            }

            fwrite(strJson.c_str(), sizeof(char), strJson.size(), out);

            fclose(in);
            fclose(out);

            // Create sqlite database
            FILE *fp = fopen("db.sqlite", "w");
            if (fp == NULL) {
                return -2;
            }
            fclose(fp);

            DataStorage::Database::DbConnectionConfig dbdata;
            DataStorage::Database::ConnectionAbstract *db = DataStorage::Database::create_connection(dbdata);
            if (db == NULL) {
                return -2;
            }

            // DbSchema *schema = DbSchema::fromJson(jsonString);
            // QueryBuilder::createFromSchema(schema);
                // QueryBuilder query = QueryBuilder(db, false);
                // query.createTable()
                //  .field()
                //  .field()
                // query->execute();

            DataStorage::Database::close(db, dbdata);
            free(db);
            DataStorage::Database::free_DbConnectionConfig(&dbdata);

            return 0;
        }

        void installApplication(int argc, char** argv)
        {
            Models::InstallType type = (Models::InstallType)atoi(Utils::ArrayUtils::get_arg("-t", argv, argc));

            int status = 0;
            if (type == Models::InstallType::WEB) {
                status = installWeb();
            }
            else {
                status = installLocal();
            }

            if (status == 0) {
                printf("Application successfully installed\n");
            }
            else {
                printf("Application installation failed\n");
            }
        }

        void parseConfigFile()
        {
            FILE *fp = fopen("config.json", "r");

            nlohmann::json config = nlohmann::json::parse(fp);
        }
    }
}

#endif