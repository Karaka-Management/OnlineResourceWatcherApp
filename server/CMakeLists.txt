cmake_minimum_required(VERSION 3.22)
project(OnlineResourceWatcherServerApp VERSION 1.0.0 LANGUAGES CXX)
add_executable(OnlineResourceWatcherServerApp main.cpp)

set(CMAKE_BUILD_TYPE "Debug")
set(CMAKE_CXX_FLAGS "-march=native -msse2 -mavx -maes")

set(CMAKE_CXX_STANDARD 17)
set(CMAKE_CXX_STANDARD_REQUIRED ON)
set(CMAKE_AUTOMOC ON)
set(CMAKE_AUTORCC ON)
set(CMAKE_AUTOUIC ON)
set(CMAKE_INCLUDE_CURRENT_DIR ON)

include_directories(/usr/include)
link_directories(/usr/lib)
link_directories(/usr/lib/x86_64-linux-gnu)

# SQLite3
target_link_libraries(OnlineResourceWatcherServerApp PRIVATE sqlite3)

# MariaDB
target_link_libraries(OnlineResourceWatcherServerApp PRIVATE mysqlclient)

# Postgresql
target_include_directories(OnlineResourceWatcherServerApp PRIVATE /usr/include/postgresql)
target_link_directories(OnlineResourceWatcherServerApp PRIVATE /usr/lib/postgresql/10/lib)
target_link_libraries(OnlineResourceWatcherServerApp PRIVATE pq)

# Libcurl
target_link_libraries(OnlineResourceWatcherServerApp PRIVATE curl)

# cmake -DCMAKE_BUILD_TYPE=Debug -DOMS_DEBUG=true
# cmake -DCMAKE_BUILD_TYPE=Release -DOMS_DEMO=true