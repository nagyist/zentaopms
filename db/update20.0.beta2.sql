UPDATE `zt_workflowfield` SET `default` = 0 WHERE `field` = 'approva' AND `role` = 'approval' AND `default` = '';
UPDATE `zt_workflowfield` SET `default` = 'wait' WHERE `field` = 'reviewStatus' AND `role` = 'approval' AND `default` = '';

ALTER TABLE `zt_project` ADD COLUMN `enabled` enum('on','off') NOT NULL DEFAULT 'on' AFTER `parallel`;
ALTER TABLE `zt_object`  ADD COLUMN `enabled` enum('0','1')    NOT NULL DEFAULT '1'  AFTER `type`;

INSERT INTO `zt_metric`(`purpose`, `scope`, `object`, `stage`, `type`, `name`, `code`, `alias`, `unit`, `desc`, `definition`, `when`, `createdBy`, `createdDate`, `builtin`, `deleted`, `dateType`)  VALUES
('scale', 'project', 'execution', 'released', 'php', '按项目统计的年度完成执行数', 'count_of_annual_finished_execution_in_project', '完成执行数', 'count', '按项目统计的年度完成执行数是指项目在某年度已经完成的执行数。该度量项反映了项目团队在某年的工作效率和完成能力。较高的年度完成执行数表示团队在完成任务方面表现出较高的效率，反之则可能需要审查工作流程和资源分配情况，以提高执行效率。', '项目的执行个数求和\r\n实际完成日期为某年\r\n过滤已删除的执行\r\n过滤已删除的项目', 'realtime', 'system', '2023-08-22 08:00:00', '1', '0', 'year');
