<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

/**
 * A Matcher Interface primarily for mocking.
 *
 */
interface IMatcher
{
    /**
     * Set the options.
     *
     * @param array $options The options
     */
    public function setOptions(array $options): self;

    /**
     * Get the options.
     */
    public function getOptions(): array;

    /**
     * Reset cached results.
     */
    public function resetCachedResults(): self;

    /**
     * Set the first and second sequences to use with the sequence matcher.
     *
     * This method is more effecient than "->setSeq1($old)->setSeq2($new)"
     * because it only run the routine once.
     *
     * @param string[] $a an array containing the lines to compare against
     * @param string[] $b an array containing the lines to compare
     */
    public function setSequences(array $a, array $b): self;

    /**
     * Set the first sequence ($a) and reset any internal caches to indicate that
     * when calling the calculation methods, we need to recalculate them.
     *
     * @param string[] $a the sequence to set as the first sequence
     */
    public function setSeq1(array $a): self;

    /**
     * Set the second sequence ($b) and reset any internal caches to indicate that
     * when calling the calculation methods, we need to recalculate them.
     *
     * @param string[] $b the sequence to set as the second sequence
     */
    public function setSeq2(array $b): self;

    /**
     * Find the longest matching block in the two sequences, as defined by the
     * lower and upper constraints for each sequence. (for the first sequence,
     * $alo - $ahi and for the second sequence, $blo - $bhi).
     *
     * Essentially, of all of the maximal matching blocks, return the one that
     * startest earliest in $a, and all of those maximal matching blocks that
     * start earliest in $a, return the one that starts earliest in $b.
     *
     * If the junk callback is defined, do the above but with the restriction
     * that the junk element appears in the block. Extend it as far as possible
     * by matching only junk elements in both $a and $b.
     *
     * @param int $alo the lower constraint for the first sequence
     * @param int $ahi the upper constraint for the first sequence
     * @param int $blo the lower constraint for the second sequence
     * @param int $bhi the upper constraint for the second sequence
     *
     * @return int[] an array containing the longest match that includes the starting position in $a, start in $b and the length/size
     */
    public function findLongestMatch(int $alo, int $ahi, int $blo, int $bhi): array;

    /**
     * Return a nested set of arrays for all of the matching sub-sequences
     * in the strings $a and $b.
     *
     * Each block contains the lower constraint of the block in $a, the lower
     * constraint of the block in $b and finally the number of lines that the
     * block continues for.
     *
     * @return int[][] a nested array of the matching blocks, as described by the function
     */
    public function getMatchingBlocks(): array;

    /**
     * Return a list of all of the opcodes for the differences between the
     * two strings.
     *
     * The nested array returned contains an array describing the opcode
     * which includes:
     * 0 - The type of tag (as described below) for the opcode.
     * 1 - The beginning line in the first sequence.
     * 2 - The end line in the first sequence.
     * 3 - The beginning line in the second sequence.
     * 4 - The end line in the second sequence.
     *
     * The different types of tags include:
     * replace - The string from $i1 to $i2 in $a should be replaced by
     *           the string in $b from $j1 to $j2.
     * delete -  The string in $a from $i1 to $j2 should be deleted.
     * insert -  The string in $b from $j1 to $j2 should be inserted at
     *           $i1 in $a.
     * equal  -  The two strings with the specified ranges are equal.
     *
     * @return int[][] array of the opcodes describing the differences between the strings
     */
    public function getOpcodes(): array;

    /**
     * Return a series of nested arrays containing different groups of generated
     * opcodes for the differences between the strings with up to $context lines
     * of surrounding content.
     *
     * Essentially what happens here is any big equal blocks of strings are stripped
     * out, the smaller subsets of changes are then arranged in to their groups.
     * This means that the sequence matcher and diffs do not need to include the full
     * content of the different files but can still provide context as to where the
     * changes are.
     *
     * @param int $context the number of lines of context to provide around the groups
     *
     * @return int[][][] nested array of all of the grouped opcodes
     */
    public function getGroupedOpcodes(int $context = 3): array;

    /**
     * Return a measure of the similarity between the two sequences.
     * This will be a float value between 0 and 1.
     *
     * Out of all of the ratio calculation functions, this is the most
     * expensive to call if getMatchingBlocks or getOpcodes is yet to be
     * called. The other calculation methods (quickRatio and realQuickRatio)
     * can be used to perform quicker calculations but may be less accurate.
     *
     * The ratio is calculated as (2 * number of matches) / total number of
     * elements in both sequences.
     *
     * @return float the calculated ratio
     */
    public function ratio(): float;

}