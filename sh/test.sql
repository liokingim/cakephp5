WITH tr AS (
    -- 당일 조회기준 데이터
    WITH trByDate AS (
        SELECT tr_tmp.*
        FROM tr_train_result tr_tmp
        WHERE tr_tmp.travelDate = '2024-11-05'
    )
    SELECT * FROM trByDate
    WHERE (arrivalDateTime IS NOT NULL AND ownerCompanyCodeTravel = '0')
        OR (arrivalDateTime IS NULL AND stationName2 = nextStation AND ownerCompanyCodeTravel = '0')
    UNION ALL
    -- 임의로 조건을 넣어 조회한 데이터
    SELECT tr.* FROM trByDate tr
    INNER JOIN m_station s1 ON tr.stationName1 = s1.stationName AND NOT s1.deleteFlag
    INNER JOIN m_station s2 ON tr.stationName2 = s2.stationName AND NOT s2.deleteFlag
    INNER JOIN m_train_result_complement trc ON (
        tr.arrivalDateTime IS NULL AND NOT trc.deleteFlag AND
        (tr.trainlineCodeTravel = trc.trainlineCodeTravel OR trc.trainlineCodeTravel IS NULL) AND
        (tr.rattleNo = trc.rattleNo OR trc.rattleNo IS NULL) AND
        (tr.businessDayTypeTravelSeq = trc.businessDayTypeTravelSeq OR trc.businessDayTypeTravelSeq IS NULL) AND
        (tr.carLineCount = trc.carLineCount OR trc.carLineCount IS NULL) AND
        (tr.trainTypeCode = trc.trainTypeCode OR trc.trainTypeCode IS NULL) AND
        (tr.upDown = trc.upDown OR trc.upDown IS NULL) AND
        (tr.stationName1 = trc.stationName1 OR trc.stationName1 IS NULL) AND
        (tr.stationName2 = trc.stationName2 OR trc.stationName2 IS NULL) AND
        (tr.doorCount = trc.doorCount OR trc.doorCount IS NULL) AND
        (tr.previousStation = trc.previousStation OR trc.previousStation IS NULL) AND
        (tr.nextStation = trc.nextStation OR trc.nextStation IS NULL) AND
        (tr.ownerCompanyCodeTravel = trc.ownerCompanyCodeTravel OR trc.ownerCompanyCodeTravel IS NULL) AND
        (tr.driveOrganize = trc.driveOrganize OR trc.driveOrganize IS NULL) AND
        (tr.carNo IN (trc.carNo1, trc.carNo2, trc.carNo3, trc.carNo4, trc.carNo5, trc.carNo6) OR trc.carNo IS NULL) AND
        (s1.ownerCompanyCode = s1.ownerCompanyCode1 OR s1.ownerCompanyCode IS NULL) AND
        (s2.ownerCompanyCode = s2.ownerCompanyCode2 OR s2.ownerCompanyCode IS NULL) AND
        ((trc.nextStationNullFlag AND trc.nextStation IS NULL) OR NOT trc.nextStationNullFlag) AND
        ((trc.retsujoNullFlag AND tr.ownerCompanyCodeTravel IS NULL) OR NOT trc.retsujoNullFlag)
    )
)
SELECT
    cc.courserIdAfter,
    wk1.travelDate,
    wk1.tarrlerNo,
    ROW_NUMBER() OVER(PARTITION BY cc.courserIdAfter, wk1.travelDate, wk1.rattlerNo, wk1.stationName2, ORDER BY wk1.trainNo ASC),
    wk1.stationName1,
    wk1.stationName2,
    wk1.arrivalDateTime,
    wk1.trainNo,
    sm.mileage,
    NOW(),
    NOW(),
    "B002",
    "B002",
    FALSE,
    0
FROM
(
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo1 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo1) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo1 != '0' AND c.trainNo IS NOT NULL
    UNION
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo2 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo2) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo2 != '0' AND c.trainNo IS NOT NULL
    UNION
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo3 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo3) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo3 != '0' AND c.trainNo IS NOT NULL
    UNION
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo4 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo4) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo4 != '0' AND c.trainNo IS NOT NULL
    UNION
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo5 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo5) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo5 != '0' AND c.trainNo IS NOT NULL
    UNION
    SELECT tr.trainlineCodeTravel,
           tr.travelDate,
           tr.rattleNo,
           tr.stationName1,
           tr.stationName2,
           tr.trainNo,
           tr.arrivalDateTime
    FROM tr
    LEFT JOIN m_carno_change carnoChange ON carNo6 = carnoChange.carNoBefore AND NOT carnoChange.deleteFlag
    INNER JOIN m_car c ON IFNULL(carnoAfter, carNo6) = c.carNo AND NOT c.deleteFlag
    WHERE tr.carNo6 != '0' AND c.trainNo IS NOT NULL
) wk1
INNER JOIN m_course_change cc ON wk1.trainlineCodeTravel = cc.courseIdBefore AND NOT cc.deleteFlag
LEFT JOIN m_station_mileage sm ON wk1.stationName1 = sm.stationName AND wk1.stationName2 = sm.stationName2 AND NOT sm.deleteFlag
GROUP BY cc.courseIdAfter, wk1.travelDate, wk1.rattleNo, wk1.stationName1, wk1.stationName2, wk1.trainNo, wk1.arrivalDateTime;
