/**
 * Karaka
 *
 * @package   Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
#ifndef MODELS_RESOURCE_MAPPER_H
#define MODELS_RESOURCE_MAPPER_H

#include <stdio.h>
#include <stdlib.h>

#include "../cOMS/DataStorage/Database/Mapper/DataMapperTypes.h"
#include "../cOMS/DataStorage/Database/Mapper/MapperAbstract.h"

namespace Models {
    static const DataStorage::Database::MapperData ResourceMapper = {
        .MEMBER_COUNT = 1,
        .MODEL_STRUCTURE = new DataStorage::Database::ModelStructure[1] {
            {.name = "id", .size = sizeof(int)}
        },

        .COLUMN_COUNT = 2,
        .COLUMNS = new DataStorage::Database::DataMapperColumn[2] {
            {.name = "orw_resource_id", .type = DataStorage::Database::FieldType::FIELD_TYPE_INT, .internal = "title"},
            {.name = "orw_resource_status", .type = DataStorage::Database::FieldType::FIELD_TYPE_INT, .internal = "status"}
        }
    };
}

#endif
