#!/bin/bash

# 원본 서버의 MySQL 정보
SOURCE_HOST="source_host"
SOURCE_USER="source_user"
SOURCE_PASS="source_password"
SOURCE_DB="source_db"

# 대상 서버의 MySQL 정보
TARGET_HOST="target_host"
TARGET_USER="target_user"
TARGET_PASS="target_password"
TARGET_DB="target_db"

# 한번에 처리할 레코드 수를 설정 (1000건씩 전송)
BATCH_SIZE=1000

# 테이블 목록 배열
TABLES=("table1" "table2" "table3" "table4" "table5" "table6" "table7" "table8" "table9" "table10"
        "table11" "table12" "table13" "table14" "table15" "table16" "table17" "table18" "table19" "table20"
        # ... 여기에서 총 100개의 테이블을 추가
        "table99" "table100")

# 각 테이블에 대해 반복
for TABLE in "${TABLES[@]}"
do
    echo "==============================="
    echo "테이블: $TABLE 전송 시작"
    echo "==============================="

    # 원본 테이블의 구조를 추출하여 대상 서버에 테이블 생성
    echo "원본 테이블 $TABLE의 구조 복사 중..." > /dev/null
    TABLE_SCHEMA=$(mysqldump -h $SOURCE_HOST -u $SOURCE_USER -p$SOURCE_PASS --no-data $SOURCE_DB $TABLE)
    echo "$TABLE_SCHEMA" | mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB

    # 해당 테이블의 총 레코드 수를 가져옴
    TOTAL_RECORDS=$(mysql -h $SOURCE_HOST -u $SOURCE_USER -p$SOURCE_PASS $SOURCE_DB -se "SELECT COUNT(*) FROM $TABLE;")
    echo "총 레코드 수 (테이블 $TABLE): $TOTAL_RECORDS" > /dev/null

    # 초기 OFFSET 값 설정
    OFFSET=0

    # 데이터가 남아 있는 동안 반복
    while [ $OFFSET -lt $TOTAL_RECORDS ]
    do
        # 데이터 전송 쿼리 (LIMIT과 OFFSET을 사용하여 분리 처리)
        QUERY="INSERT INTO ${TARGET_DB}.${TABLE} SELECT * FROM ${SOURCE_DB}.${TABLE} LIMIT $BATCH_SIZE OFFSET $OFFSET;"

        # 트랜잭션 시작
        mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB -e "START TRANSACTION;" > /dev/null

        # 쿼리 실행
        mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB -e "$QUERY" > /dev/null

        # 오류 체크
        if [ $? -eq 0 ]; then
            # 트랜잭션 커밋
            mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB -e "COMMIT;" > /dev/null
        else
            # 트랜잭션 롤백
            mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB -e "ROLLBACK;" > /dev/null
            echo "ERROR: 테이블 $TABLE, OFFSET $OFFSET 부터 $BATCH_SIZE 레코드 전송 실패." >&2
            exit 1
        fi

        # OFFSET을 BATCH_SIZE 만큼 증가시켜 다음 배치로 이동
        OFFSET=$((OFFSET + BATCH_SIZE))
    done

    # 원본 테이블의 인덱스 및 제약 조건 복사
    echo "원본 테이블 $TABLE의 인덱스 복사 중..." > /dev/null
    INDEXES=$(mysqldump -h $SOURCE_HOST -u $SOURCE_USER -p$SOURCE_PASS --no-data --skip-create-info --no-data $SOURCE_DB $TABLE)
    echo "$INDEXES" | mysql -h $TARGET_HOST -u $TARGET_USER -p$TARGET_PASS $TARGET_DB

    # 완료 메시지 출력
    echo "==============================="
    echo "테이블 $TABLE 데이터 전송 완료"
    echo "==============================="
done

echo "모든 테이블 데이터 전송 완료!"
